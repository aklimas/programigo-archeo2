<?php

namespace App\Controller\Other\Settlement;

use App\Entity\Other\Settlement\SettlementStatus;
use App\Form\Other\Settlement\SettlementStatusType;
use App\Repository\Other\Settlement\SettlementStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/settlement/status")]
class SettlementStatusController extends AbstractController
{

    public function __construct(
        private HelperService $helper,
        private SettlementStatusRepository $settlement,
        private $buttons = [],
        private string $title = 'Settlement')
    {

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route("/", name: "settlementStatus_list")]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'settlementStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('other/settlement/status/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Lista']
            ],
        ]);
    }

    #[Route("/json", name: "settlementStatus_jsonList", methods: ["GET"])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->settlement->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('settlementStatus_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

                array_push($data, $dat);
            }
            $jsonData = [
                'data' => $data,
            ];

            return new JsonResponse($jsonData);
        } else {
            throw new NotFoundHttpException('Page not found');
        }
    }

    #[Route("/nowy", name: "settlementStatus_new",methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $settlementStatus = new SettlementStatus();
        $form = $this->createForm(SettlementStatusType::class, $settlementStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->settlement->save($settlementStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('settlementStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'settlementStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/status/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'settlement_status' => $settlementStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Nowy']
            ],
        ]);
    }

    #[Route("/{id}/edytuj", name: "settlementStatus_edit",methods: ['GET','POST'])]
    public function edit(Request $request, SettlementStatus $settlementStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(SettlementStatusType::class, $settlementStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->settlement->save($settlementStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('settlementStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'settlementStatusVariants_list',
            'button_param' => ['id' => $settlementStatus->getId()],
            'button_title' => 'Zależności: '.$settlementStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'settlementStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/status/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'settlement_status' => $settlementStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Edycja']
            ],
        ]);
    }

    #[Route("/{id}", name: "settlementStatus_delete", methods: ["POST"])]
    public function delete(Request $request, SettlementStatus $settlementStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$settlementStatus->getId(), $request->request->get('_token'))) {
            $this->settlement->remove($settlementStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('settlementStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
