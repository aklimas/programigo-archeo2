<?php

namespace App\Controller\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplateStatus;
use App\Form\Core\SmsTemplate\SmsTemplateStatusType;
use App\Repository\Core\SmsTemplate\SmsTemplateStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/smsTemplate/status')]
class SmsTemplateStatusController extends AbstractController
{
    private SmsTemplateStatusRepository $smsTemplate;
    private $buttons;
    private HelperService $helper;

    public function __construct(HelperService $helperService, SmsTemplateStatusRepository $smsTemplate)
    {
        $this->smsTemplate = $smsTemplate;
        $this->helper = $helperService;
        $this->buttons = [];

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/', name: 'smsTemplateStatus_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'smsTemplateStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('smsTemplate/status/list.html.twig', [
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/json', name: 'smsTemplateStatus_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->smsTemplate->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('smsTemplateStatus_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

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

    #[Route('/nowy', name: 'smsTemplateStatus_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $smsTemplateStatus = new SmsTemplateStatus();
        $form = $this->createForm(SmsTemplateStatusType::class, $smsTemplateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->smsTemplate->save($smsTemplateStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('smsTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'smsTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/status/new.html.twig', [
            'smsTemplate_status' => $smsTemplateStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj', name: 'smsTemplateStatus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SmsTemplateStatus $smsTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(SmsTemplateStatusType::class, $smsTemplateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->smsTemplate->save($smsTemplateStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('smsTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'smsTemplateStatusVariants_list',
            'button_param' => ['id' => $smsTemplateStatus->getId()],
            'button_title' => 'Zależności: '.$smsTemplateStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'smsTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/status/edit.html.twig', [
            'smsTemplate_status' => $smsTemplateStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['smsTemplate_list' => 'Szablony Sms'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'smsTemplateStatus_delete', methods: ['POST'])]
    public function delete(Request $request, SmsTemplateStatus $smsTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$smsTemplateStatus->getId(), $request->request->get('_token'))) {
            $this->smsTemplate->remove($smsTemplateStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('smsTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
