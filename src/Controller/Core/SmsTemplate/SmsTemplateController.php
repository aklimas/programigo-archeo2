<?php

namespace App\Controller\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplate;
use App\Form\Core\SmsTemplate\SmsTemplateType;
use App\Repository\Core\LogsRepository;
use App\Repository\Core\SmsTemplate\SmsTemplateRepository;
use App\Service\HelperService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/szablony-sms')]
class SmsTemplateController extends AbstractController
{
    private SmsTemplateRepository $smsTemplate;
    private $buttons;
    private UserService $user;
    private LogsRepository $logs;
    private HelperService $helper;
    private string $title;

    public function __construct(
        HelperService $helperService,
        SmsTemplateRepository $smsTemplate,
        UserService $userService,
        Security $security,
        LogsRepository $logsRepository)
    {
        $this->smsTemplate = $smsTemplate;
        $this->helper = $helperService;
        $this->logs = $logsRepository;
        $this->user = $userService;
        $this->buttons = [];

        $this->title = 'Szablony sms';

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
            'button_link' => 'smsTemplate_new',
            'button_title' => 'Dodaj szablon',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'smsTemplateStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'smsTemplate_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons

        return $this->render('smsTemplate/list.html.twig', [
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['smsTemplate_list' => $this->title],
            ],
        ]);
    }

    #[Route('/json', name: 'smsTemplate_jsonList', methods : ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->smsTemplate->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['title'] = $item->getTitle();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                $dat['dateModify'] = $item->getDateModify()->format('d-m-Y H:i');
                $dat['status'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('smsTemplate_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

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

    #[Route('/nowy', name: 'smsTemplate_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $smsTemplate = new SmsTemplate();
        $form = $this->createForm(SmsTemplateType::class, $smsTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Szablony sms';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Dodano nowy szablon sms';

            $this->smsTemplate->save($smsTemplate, true);

            $this->addFlash('success', 'Wpis zapisany');

            $this->logs->add($log);

            return $this->redirectToRoute('smsTemplate_edit', ['id' => $smsTemplate->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'smsTemplate_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/new.html.twig', [
            'smsTemplate' => $smsTemplate,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['smsTemplate_list' => $this->title],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj', name: 'smsTemplate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SmsTemplate $smsTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // $oldStatus = $smsTemplate->getStatus();

        $form = $this->createForm(SmsTemplateType::class, $smsTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'SmsTemplate';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Edycja szablonu sms '.$smsTemplate->getTitle();

            $this->smsTemplate->save($smsTemplate, true);

            $this->logs->add($log);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('smsTemplate_edit', ['id' => $smsTemplate->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'smsTemplate_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('smsTemplate/edit.html.twig', [
            'smsTemplate' => $smsTemplate,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['smsTemplate_list' => $this->title],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'smsTemplate_delete', methods: ['POST'])]
    public function delete(Request $request, SmsTemplate $smsTemplate): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$smsTemplate->getId(), $request->request->get('_token'))) {
            $log = [];
            $log['name'] = 'SmsTemplate';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Usunięcie SmsTemplate '.$smsTemplate->getTitle();

            $this->smsTemplate->remove($smsTemplate, true);

            $this->logs->add($log);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('smsTemplate_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sendTestSMS/{id}', name: 'smsTemplate_sendTestEmail', methods: ['GET'])]
    public function sendTestEmail(SmsTemplate $smsTemplate, TexterInterface $texter): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $phone = $this->user->userData($this->getUser())->getUserData()->getPhone();

        if ($phone) {
            $smsContent = $smsTemplate->getContent();
            $smsContent = $this->user->replaceData($this->getUser(), $smsContent);

            // wysyłamy SMS
            $sms = new SmsMessage($phone, $smsContent);
            $sentMessage = $texter->send($sms);
            $this->addFlash('success', 'SMS testowy wysłany');
        } else {
            $this->addFlash('danger', 'Numer telefonu nieprawidłowy');
        }

        // return new JsonResponse();
        return $this->redirectToRoute('smsTemplate_edit', ['id' => $smsTemplate->getId()], Response::HTTP_SEE_OTHER);
    }
}
