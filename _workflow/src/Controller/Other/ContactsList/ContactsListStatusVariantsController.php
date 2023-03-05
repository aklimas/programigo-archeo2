<?php

namespace App\Controller\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListStatus;
use App\Entity\Other\ContactsList\ContactsListStatusVariants;
use App\Form\Other\ContactsList\ContactsListStatusType;
use App\Form\Other\ContactsList\ContactsListStatusVariantsType;
use App\Repository\Other\ContactsList\ContactsListStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/contactsList/status/variants")]
class ContactsListStatusVariantsController extends AbstractController
{

    public function __construct(
        private ContactsListStatusVariantsRepository $contactsList,
        private $buttons = [],
        private $title = 'ContactsList'
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

    #[Route("/{id}", name: "contactsListStatusVariants_list")]
    public function list(ContactsListStatus $contactsListStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'contactsListStatus_edit',
            'button_param' => ['id' => $contactsListStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'contactsListStatusVariants_new',
            'button_param' => ['id' => $contactsListStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('other/contactsList/status/variants/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $contactsListStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Lista'],
            ],
            'contactsListStatus' => $contactsListStatus,
        ]);
    }

    #[Route("/{id}/json/", name: "contactsListStatusVariants_jsonList", methods: ["GET"])]
    public function jsonList(ContactsListStatus $contactsListStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->contactsList->findBy(['status' => $contactsListStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('contactsListStatusVariants_edit', ['status' => $contactsListStatus->getId(), 'id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route("/{id}/nowy", name: "contactsListStatusVariants_new",methods: ['GET','POST'])]
    public function new(Request $request, ContactsListStatus $contactsListStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $contactsListStatusVariants = new ContactsListStatusVariants();
        $form = $this->createForm(ContactsListStatusVariantsType::class, $contactsListStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactsListStatusVariants->setStatus($contactsListStatus);
            $this->contactsList->save($contactsListStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('contactsListStatusVariants_list', ['id' => $contactsListStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'contactsListStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/status/variants/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'contactsList_status' => $contactsListStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $contactsListStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route("/edytuj/{status}/{id}/", name: "contactsListStatusVariants_edit",methods: ['GET','POST'])]
    public function edit(Request $request, ContactsListStatus $status, ContactsListStatusVariants $contactsListStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(ContactsListStatusVariantsType::class, $contactsListStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactsList->save($contactsListStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('contactsListStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'contactsListStatus_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/status/variants/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'contactsList_status_variants' => $contactsListStatusVariants,
            'contactsList_status' => $status,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $status->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route("/delete/{status}/{id}", name: "contactsListStatusVariants_delete", methods: ["POST"])]
    public function delete(Request $request, ContactsListStatus $status, ContactsListStatusVariants $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->request->get('_token'))) {
            $this->contactsList->remove($id, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('contactsListStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
    }
}
