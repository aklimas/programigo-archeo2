<?php

namespace App\Controller\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListStatus;
use App\Form\Other\ContactsList\ContactsListStatusType;
use App\Repository\Other\ContactsList\ContactsListStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/contactsList/status")]
class ContactsListStatusController extends AbstractController
{

    public function __construct(
        private HelperService $helper,
        private ContactsListStatusRepository $contactsList,
        private $buttons = [],
        private string $title = 'ContactsList')
    {

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route("/", name: "contactsListStatus_list")]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'contactsListStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('other/contactsList/status/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Lista']
            ],
        ]);
    }

    #[Route("/json", name: "contactsListStatus_jsonList", methods: ["GET"])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->contactsList->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('contactsListStatus_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route("/nowy", name: "contactsListStatus_new",methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $contactsListStatus = new ContactsListStatus();
        $form = $this->createForm(ContactsListStatusType::class, $contactsListStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactsList->save($contactsListStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('contactsListStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'contactsListStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/status/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'contactsList_status' => $contactsListStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Nowy']
            ],
        ]);
    }

    #[Route("/{id}/edytuj", name: "contactsListStatus_edit",methods: ['GET','POST'])]
    public function edit(Request $request, ContactsListStatus $contactsListStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(ContactsListStatusType::class, $contactsListStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactsList->save($contactsListStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('contactsListStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'contactsListStatusVariants_list',
            'button_param' => ['id' => $contactsListStatus->getId()],
            'button_title' => 'Zależności: '.$contactsListStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'contactsListStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/status/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'contactsList_status' => $contactsListStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Edycja']
            ],
        ]);
    }

    #[Route("/{id}", name: "contactsListStatus_delete", methods: ["POST"])]
    public function delete(Request $request, ContactsListStatus $contactsListStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$contactsListStatus->getId(), $request->request->get('_token'))) {
            $this->contactsList->remove($contactsListStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('contactsListStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
