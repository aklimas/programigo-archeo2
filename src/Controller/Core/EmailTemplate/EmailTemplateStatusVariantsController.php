<?php

namespace App\Controller\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplateStatus;
use App\Entity\Core\EmailTemplate\EmailTemplateStatusVariants;
use App\Form\Core\EmailTemplate\EmailTemplateStatusType;
use App\Form\Core\EmailTemplate\EmailTemplateStatusVariantsType;
use App\Repository\Core\EmailTemplate\EmailTemplateStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/szablony-email/statusy/warianty')]
class EmailTemplateStatusVariantsController extends AbstractController
{
    private EmailTemplateStatusVariantsRepository $emailTemplate;
    private $buttons;
    private string $title;

    public function __construct(EmailTemplateStatusVariantsRepository $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
        $this->buttons = [];
        $this->title = 'Szablony email';

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/{id}', name: 'emailTemplateStatusVariants_list')]
    public function list(EmailTemplateStatus $emailTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplateStatus_edit',
            'button_param' => ['id' => $emailTemplateStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplateStatusVariants_new',
            'button_param' => ['id' => $emailTemplateStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('emailTemplate/status/variants/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $emailTemplateStatus->getName()],
                ['none' => 'Warianty'],
            ],
            'emailTemplateStatus' => $emailTemplateStatus,
        ]);
    }

    #[Route('/{id}/json/', name: 'emailTemplateStatusVariants_jsonList', methods: ['GET'])]
    public function jsonList(EmailTemplateStatus $emailTemplateStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->emailTemplate->findBy(['status' => $emailTemplateStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('emailTemplateStatusVariants_edit', ['status' => $emailTemplateStatus->getId(), 'id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

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

    #[Route('/{id}/nowy', name: 'emailTemplateStatusVariants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmailTemplateStatus $emailTemplateStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $emailTemplateStatusVariants = new EmailTemplateStatusVariants();
        $form = $this->createForm(EmailTemplateStatusVariantsType::class, $emailTemplateStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailTemplateStatusVariants->setStatus($emailTemplateStatus);
            $this->emailTemplate->save($emailTemplateStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('emailTemplateStatusVariants_list', ['id' => $emailTemplateStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'emailTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('emailTemplate/status/variants/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'emailTemplate_status' => $emailTemplateStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $emailTemplateStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{status}/{id}/edytuj', name: 'emailTemplateStatusVariants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EmailTemplateStatus $emailTemplateStatus, EmailTemplateStatusVariants $emailTemplateStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(EmailTemplateStatusType::class, $emailTemplateStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailTemplate->save($emailTemplateStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('emailTemplateStatusVariants_list', [], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'emailTemplateStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('emailTemplate/status/variants/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'emailTemplate_status' => $emailTemplateStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $emailTemplateStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'emailTemplateStatus_delete', methods: ['POST'])]
    public function delete(Request $request, EmailTemplateStatusVariants $emailTemplateStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$emailTemplateStatusVariants->getId(), $request->request->get('_token'))) {
            $this->emailTemplate->remove($emailTemplateStatusVariants, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('emailTemplateStatusVariants_list', [], Response::HTTP_SEE_OTHER);
    }
}
