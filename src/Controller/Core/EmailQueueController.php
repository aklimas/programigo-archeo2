<?php

namespace App\Controller\Core;

use App\Repository\Core\EmailQueueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EmailQueueController extends AbstractController
{
    #[Route('/kolejka-email', name: 'emailQueue_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('core/emailQueue/list.html.twig', [
            'buttons' => [
                [
                    'button_class' => 'btn-secondary',
                    'button_icon' => 'ri-home-2-fill',
                    'button_link' => 'home',
                    'button_title' => '',
                ],
            ],
            'breadcrumb' => [
                ['none' => 'Administracja'],
                ['none' => 'Kolejka email'],
            ],
        ]);
    }

    #[Route('/jsonEmailQueueList', name: 'emailQueue_json_list')]
    public function jsonList(Request $request, EmailQueueRepository $emailQueueRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $dd = '';

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $data = [];
            $return = $emailQueueRepository->findBy([], ['createDate' => 'DESC']);
            foreach ($return as $item) {

                $dat = $item->getCreateDate();
                if (is_object($dat)) {
                    $dd = $dat->format('d-m-Y H:i:s');
                }else{
                    $dd = '-';
                }

                $dat = $item->getPostDate();
                if (is_object($dat)) {
                    $dd2 = $dat->format('d-m-Y H:i:s');
                } else {
                    $dd2 = '-';
                }

                array_push($data, [
                    'id' => $item->getId(),
                    'subject' => $item->getSubject(),
                    'content' => $item->getContent(),
                    'senderEmail' => $item->getSenderEmail(),
                    'senderLabel' => $item->getSenderLabel(),
                    'recipient' => $item->getRecipient(),
                    'recipientLabel' => $item->getRecipientLabel(),
                    'status' => $item->getStatus(),
                    'type' => $item->getType(),
                    'template' => $item->getTemplate(),
                    'dateSend' => $dd2,
                    'dateCreate' => $dd,
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
