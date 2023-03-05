<?php

namespace App\Controller\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplateStatus;
use App\Entity\Core\SmsTemplate\SmsTemplateStatusVariants;
use App\Form\Core\SmsTemplate\SmsTemplateStatusType;
use App\Form\Core\SmsTemplate\SmsTemplateStatusVariantsType;
use App\Repository\Core\SmsTemplate\SmsTemplateStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/smsTemplate/status/variants')]
class SmsTemplateStatusVariantsController extends AbstractController
{
    private SmsTemplateStatusVariantsRepository $smsTemplate;
    private $buttons;

    public function __construct(SmsTemplateStatusVariantsRepository $smsTemplate)
    {
        $this->smsTemplate = $smsTemplate;
        $this->buttons = [];

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/{id}', name: 'smsTemplateStatusVariants_list')]
    public function list(SmsTemplateStatus $smsTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'smsTemplateStatus_edit',
            'button_param' => ['id' => $smsTemplateStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'smsTemplateStatusVariants_new',
            'button_param' => ['id' => $smsTemplateStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('smsTemplate/status/variants/list.html.twig', [
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
                ['none' => $smsTemplateStatus->getName()],
                ['none' => 'Warianty'],
            ],
            'worklowStatus' => $smsTemplateStatus,
        ]);
    }

    #[Route('/{id}/json/', name: 'smsTemplateStatusVariants_jsonList', methods: ['GET'])]
    public function jsonList(SmsTemplateStatus $smsTemplateStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->smsTemplate->findBy(['status' => $smsTemplateStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('smsTemplateStatusVariants_edit', ['status' => $smsTemplateStatus->getId(), 'id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

                array_push($data, $dat);
            }
            $jsonData = [
                'data' => $data,
            ];

            return new JsonResponse($jsonData);
        } else {
            return new JsonResponse();
        }
    }

    #[Route('/{id}/nowy', name: 'smsTemplateStatusVariants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SmsTemplateStatus $smsTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $smsTemplateStatusVariants = new SmsTemplateStatusVariants();
        $form = $this->createForm(SmsTemplateStatusVariantsType::class, $smsTemplateStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $smsTemplateStatusVariants->setStatus($smsTemplateStatus);
            $this->smsTemplate->save($smsTemplateStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('smsTemplateStatusVariants_list', ['id' => $smsTemplateStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'smsTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/status/variants/new.html.twig', [
            'smsTemplate_status' => $smsTemplateStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{status}/{id}/edytuj', name: 'smsTemplateStatusVariants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SmsTemplateStatus $smsTemplateStatus, SmsTemplateStatusVariants $smsTemplateStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(SmsTemplateStatusType::class, $smsTemplateStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->smsTemplate->save($smsTemplateStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('smsTemplateStatusVariants_list', [], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'smsTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/status/variants/edit.html.twig', [
            'smsTemplate_status' => $smsTemplateStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'smsTemplateStatus_delete', methods: ['POST'])]
    public function delete(Request $request, SmsTemplateStatusVariants $smsTemplateStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$smsTemplateStatusVariants->getId(), $request->request->get('_token'))) {
            $this->smsTemplate->remove($smsTemplateStatusVariants, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('smsTemplateStatusVariants_list', [], Response::HTTP_SEE_OTHER);
    }
}
