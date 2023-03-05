<?php

namespace App\Controller\Other\Settlement;

use App\Entity\Other\Settlement\SettlementStatus;
use App\Entity\Other\Settlement\SettlementStatusVariants;
use App\Form\Other\Settlement\SettlementStatusType;
use App\Form\Other\Settlement\SettlementStatusVariantsType;
use App\Repository\Other\Settlement\SettlementStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/settlement/status/variants")]
class SettlementStatusVariantsController extends AbstractController
{

    public function __construct(
        private SettlementStatusVariantsRepository $settlement,
        private $buttons = [],
        private $title = 'Settlement'
    )
    {

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);

    }

    #[Route("/{id}", name: "settlementStatusVariants_list")]
    public function list(SettlementStatus $settlementStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'settlementStatus_edit',
            'button_param' => ['id' => $settlementStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'settlementStatusVariants_new',
            'button_param' => ['id' => $settlementStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('other/settlement/status/variants/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $settlementStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Lista'],
            ],
            'settlementStatus' => $settlementStatus,
        ]);
    }

    #[Route("/{id}/json/", name: "settlementStatusVariants_jsonList", methods: ["GET"])]
    public function jsonList(SettlementStatus $settlementStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->settlement->findBy(['status' => $settlementStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('settlementStatusVariants_edit', ['status' => $settlementStatus->getId(), 'id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route("/{id}/nowy", name: "settlementStatusVariants_new",methods: ['GET','POST'])]
    public function new(Request $request, SettlementStatus $settlementStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $settlementStatusVariants = new SettlementStatusVariants();
        $form = $this->createForm(SettlementStatusVariantsType::class, $settlementStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settlementStatusVariants->setStatus($settlementStatus);
            $this->settlement->save($settlementStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('settlementStatusVariants_list', ['id' => $settlementStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'settlementStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/status/variants/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'settlement_status' => $settlementStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $settlementStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route("/edytuj/{status}/{id}/", name: "settlementStatusVariants_edit",methods: ['GET','POST'])]
    public function edit(Request $request, SettlementStatus $status, SettlementStatusVariants $settlementStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(SettlementStatusVariantsType::class, $settlementStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->settlement->save($settlementStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('settlementStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'settlementStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/status/variants/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'settlement_status_variants' => $settlementStatusVariants,
            'settlement_status' => $status,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $status->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route("/delete/{status}/{id}", name: "settlementStatusVariants_delete", methods: ["POST"])]
    public function delete(Request $request, SettlementStatus $status, SettlementStatusVariants $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->request->get('_token'))) {
            $this->settlement->remove($id, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('settlementStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
    }
}
