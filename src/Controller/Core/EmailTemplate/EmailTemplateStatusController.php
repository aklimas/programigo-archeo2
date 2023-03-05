<?php

namespace App\Controller\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplateStatus;
use App\Form\Core\EmailTemplate\EmailTemplateStatusType;
use App\Repository\Core\EmailTemplate\EmailTemplateStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/szablony-email/status')]
class EmailTemplateStatusController extends AbstractController
{
    private EmailTemplateStatusRepository $emailTemplate;
    private $buttons;
    private HelperService $helper;
    private string $title;

    public function __construct(HelperService $helperService, EmailTemplateStatusRepository $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
        $this->helper = $helperService;
        $this->buttons = [];
        $this->title = 'Szablony Email';

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/', name: 'emailTemplateStatus_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplateStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('emailTemplate/status/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/json', name: 'emailTemplateStatus_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->emailTemplate->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('emailTemplateStatus_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

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

    #[Route('/nowy', name: 'emailTemplateStatus_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $emailTemplateStatus = new EmailTemplateStatus();
        $form = $this->createForm(EmailTemplateStatusType::class, $emailTemplateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailTemplate->save($emailTemplateStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('emailTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'emailTemplateStatus_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->renderForm('emailTemplate/status/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'emailTemplate_status' => $emailTemplateStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['emailTemplateStatus_list' => 'Statusy'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj', name: 'emailTemplateStatus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EmailTemplateStatus $emailTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(EmailTemplateStatusType::class, $emailTemplateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailTemplate->save($emailTemplateStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('emailTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'emailTemplateStatusVariants_list',
            'button_param' => ['id' => $emailTemplateStatus->getId()],
            'button_title' => 'Zależności: '.$emailTemplateStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'emailTemplateStatus_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->renderForm('emailTemplate/status/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'emailTemplate_status' => $emailTemplateStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['emailTemplateStatus_list' => 'Statusy'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'emailTemplateStatus_delete', methods: ['POST'])]
    public function delete(Request $request, EmailTemplateStatus $emailTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$emailTemplateStatus->getId(), $request->request->get('_token'))) {
            $this->emailTemplate->remove($emailTemplateStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('emailTemplateStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
