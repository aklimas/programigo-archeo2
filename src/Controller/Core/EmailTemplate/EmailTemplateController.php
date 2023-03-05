<?php

namespace App\Controller\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplate;
use App\Form\Core\EmailTemplate\EmailTemplateType;
use App\Repository\Core\EmailTemplate\EmailTemplateRepository;
use App\Repository\Core\LogsRepository;
use App\Service\EmailService;
use App\Service\HelperService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/szablony-email')]
class EmailTemplateController extends AbstractController
{
    private EmailTemplateRepository $emailTemplate;
    private $buttons;
    private UserService $user;
    private EmailService $email;
    private LogsRepository $logs;
    private HelperService $helper;
    private string $title;

    public function __construct(HelperService $helperService, EmailTemplateRepository $emailTemplate, UserService $userService, EmailService $emailService, Security $security, LogsRepository $logsRepository)
    {
        $this->emailTemplate = $emailTemplate;
        $this->helper = $helperService;

        $this->logs = $logsRepository;
        $this->email = $emailService;
        $this->user = $userService;
        $this->buttons = [];

        $this->title = 'Szablony e-mail';

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplate_new',
            'button_title' => 'Dodaj szablon',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'emailTemplateStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'emailTemplate_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplate_new',
            'button_title' => 'Dodaj szablon',
        ]]);

        return $this->render('core/emailTemplate/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
            ],
        ]);
    }

    #[Route('/json', name: 'emailTemplate_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->emailTemplate->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['title'] = $item->getTitle();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                $dat['dateModify'] = $item->getDateModify()->format('d-m-Y H:i');
                $dat['status'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('emailTemplate_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

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

    #[Route('/saveTheme/{id}', name: 'emailTemplate_saveTheme', methods: ['GET', 'POST'])]
    public function saveTheme(Request $request, EmailTemplate $emailTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $emailTemplate->setContent(json_encode($request->request->get('data')));
            $emailTemplate->setContentJson($request->request->get('json'));
            $this->emailTemplate->save($emailTemplate, true);

            return new JsonResponse($request->request->get('json'));
        } else {
            throw new NotFoundHttpException('Page not found');
        }
    }

    #[Route('/nowy/{id}', name: 'emailTemplate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EmailTemplate $id = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if (null !== $id) {
            $emailTemplate = clone $id;
        } else {
            $emailTemplate = new EmailTemplate();
        }
        $emailTemplate->setTitle('');
        $emailTemplate->setContent('');
        $emailTemplate->setSubject('');

        $this->emailTemplate->save($emailTemplate, true);

        return $this->redirectToRoute('emailTemplate_edit', ['id' => $emailTemplate->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edytuj', name: 'emailTemplate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EmailTemplate $emailTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // $oldStatus = $emailTemplate->getStatus();

        $form = $this->createForm(EmailTemplateType::class, $emailTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = $this->title;
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Edycja szablonu '.$emailTemplate->getTitle();

            $this->emailTemplate->save($emailTemplate, true);

            $log['status'] = 'SUCCESS';
            $log['date_end'] = new \DateTime(); // Data zakończenia zadania
            $this->logs->add($log);

            $this->addFlash('success', 'Szablon zapisany');

            return $this->redirectToRoute('emailTemplate_edit', ['id' => $emailTemplate->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'emailTemplate_new',
            'button_title' => 'Nowy wpis',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-file-copy-2-fill',
            'button_link' => 'emailTemplate_new',
            'button_param' => ['id' => $emailTemplate->getId()],
            'button_title' => 'Duplikuj',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'emailTemplate_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/emailTemplate/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'json' => $emailTemplate->getContentJson(),
            'emailTemplate' => $emailTemplate,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['emailTemplate_list' => $this->title],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'emailTemplate_delete', methods: ['POST'])]
    public function delete(Request $request, EmailTemplate $emailTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        try {
            if ($this->isCsrfTokenValid('delete'.$emailTemplate->getId(), $request->request->get('_token'))) {
                $log = [];
                $log['name'] = $this->title;
                $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                $log['value'] = 'Usunięcie szablonu '.$emailTemplate->getTitle();

                $this->emailTemplate->remove($emailTemplate, true);

                $this->logs->add($log);
            }

            $this->addFlash('success', 'Szablon usunięty');

            return $this->redirectToRoute('emailTemplate_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('emailTemplate_edit', ['id' => $emailTemplate->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/sendTestEmail/{id}', name: 'emailTemplate_sendTestEmail', methods: ['GET'])]
    public function sendTestEmail(EmailTemplate $emailTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $emailSubject = $emailTemplate->getSubject();
        $emailSubject = $this->user->replaceData($this->getUser(), $emailSubject);

        $emailContent = json_decode($emailTemplate->getContent());
        $emailContent = $this->user->replaceData($this->getUser(), $emailContent);

        $return = $this->email->post([
            'to' => $this->user->userData($this->getUser())->getEmail(),
            'to_label' => $this->user->userData($this->getUser())->getUserData()->getName().' '.$this->user->userData($this->getUser())->getUserData()->getLastName(),
            'subject' => $emailSubject,
            'message' => $emailContent,
        ]);

        if (true === $return) {
            $this->addFlash('success', 'Email testowy wysłany');
        } else {
            $this->addFlash('danger', 'Błąd: '.$return);
        }

        // return new JsonResponse();
        return $this->redirectToRoute('emailTemplate_edit', ['id' => $emailTemplate->getId()], Response::HTTP_SEE_OTHER);
    }
}
