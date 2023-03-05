<?php

namespace App\Controller\Core;

use App\Repository\Core\LogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends AbstractController
{
    public function __construct(
        private LogsRepository $logs,
        private $title = 'Logi',
        private $buttons = []
    ) {
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/logi', name: 'logs_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('core/logs/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['none' => $this->title],
            ],
        ]);
    }

    #[Route('/jsonLogList', name: 'logs_json_list')]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $dd = '';

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $data = [];
            $logs = $this->logs->findBy([], ['date' => 'DESC']);
            foreach ($logs as $log) {
                $dat = $log->getDate();
                if (is_object($dat)) {
                    $dd = $dat->format('d-m-Y H:i:s');
                }

                $dat = $log->getDateEnd();
                if (is_object($dat)) {
                    $dd2 = $dat->format('d-m-Y H:i:s');
                } else {
                    $dd2 = '-';
                }

                array_push($data, [
                    'id' => $log->getId(),
                    'name' => $log->getName(),
                    'value' => $log->getValue(),
                    'user' => $log->getUser(),
                    'date' => $dd,
                    'date_end' => $dd2,
                    'status' => $log->getStatus(),
                ]);
            }
            $jsonData = [
                'data' => $data,
            ];

            return new JsonResponse($jsonData);
        } else {
            throw new NotFoundHttpException('Page not found');
        }
    }
}
