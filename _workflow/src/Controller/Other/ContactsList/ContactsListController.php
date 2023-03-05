<?php

namespace App\Controller\Other\ContactsList;

use App\Entity\Core\Files\Files;
use App\Entity\Other\ContactsList\ContactsList;
use App\Entity\Other\ContactsList\ContactsListHistory;
use App\Form\Other\ContactsList\ContactsListType;
use App\Repository\Core\CommentsRepository;
use App\Repository\Core\LogsRepository;
use App\Repository\Other\ContactsList\ContactsListHistoryRepository;
use App\Repository\Other\ContactsList\ContactsListRepository;
use App\Service\FilesService;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/contactsList')]
class ContactsListController extends AbstractController
{
    public function __construct(
        private HelperService     $helper,
        private ContactsListRepository $contactsList,
        // TODO Historia
        //private ContactsListHistoryRepository $contactsListHistory,
        private Security          $security,
        // TODO Pliki
        //private FilesService $files,
        private LogsRepository    $logs,
        private string            $title = 'ContactsList',
        private                   $buttons = []
    )
    {
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
            'button_link' => 'contactsList_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'contactsListStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'contactsList_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('other/contactsList/list.html.twig', [
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Lista'],
            ],
        ]);
    }

    #[Route('/json', name: 'contactsList_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->contactsList->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['title'] = $item->getTitle();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                $dat['dateModify'] = $item->getDateModify()->format('d-m-Y H:i');
                // TOTO Status
                //$dat['status'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="' . $this->generateUrl('contactsList_edit', ['id' => $item->getId()]) . '" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/nowy', name: 'contactsList_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $contactsList = new ContactsList();
        $form = $this->createForm(ContactsListType::class, $contactsList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'ContactsList';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Dodano nowy ContactsList';

            // TODO Dodawanie plików
            /*$_file = $form->get('file')->getData();
            if (null !== $_file and count($_file) > 1) {
                foreach ($_file as $f) {
                    $result = $this->files->upload($f);
                    $file = $this->files->getFile($result);
                    $contactsList->addFile($file);
                }
            } elseif (null !== $_file and count($_file) === 1) {
                $result = $this->files->upload($_file);
                $file = $this->files->getFile($result);
                $contactsList->addFile($file);
            }*/

            $this->contactsList->save($contactsList, true);

            // TODO Historia
            /*$contactsListHistory = new ContactsListHistory();
            $contactsListHistory->setValue('Utworzenie contactsList');
            $contactsListHistory->setContactsList($contactsList);
            $this->contactsListHistory->save($contactsListHistory, true);*/

            $this->addFlash('success', 'Wpis zapisany');

            $this->logs->add($log);

            return $this->redirectToRoute('contactsList_edit', ['id' => $contactsList->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'contactsList_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/new.html.twig', [
            'contactsList' => $contactsList,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj', name: 'contactsList_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactsList $contactsList): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // TOTO Status
        //$oldStatus = $contactsList->getStatus();

        $form = $this->createForm(ContactsListType::class, $contactsList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'ContactsList';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Edycja ContactsList ' . $contactsList->getTitle();

            // TODO Dodawanie plików
            /*$_file = $form->get('file')->getData();
            if (null !== $_file and count($_file) > 1) {
                foreach ($_file as $f) {
                    $result = $this->files->upload($f);
                    $file = $this->files->getFile($result);
                    $contactsList->addFile($file);
                }
            } elseif (null !== $_file and count($_file) === 1) {
                $result = $this->files->upload($_file);
                $file = $this->files->getFile($result);
                $contactsList->addFile($file);
            }*/

            $this->contactsList->save($contactsList, true);

            // TODO Historia
            /*if ($oldStatus !== $contactsList->getStatus()) {
                $statusMessage = 'Status: '.$oldStatus->getName().' > '.$contactsList->getStatus()->getName();
                $contactsListHistory = new ContactsListHistory();
                $contactsListHistory->setValue($statusMessage);
                $contactsListHistory->setContactsList($contactsList);
                $this->contactsListHistory->save($contactsListHistory, true);
            }*/

            $this->logs->add($log);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('contactsList_edit', ['id' => $contactsList->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'contactsList_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/contactsList/edit.html.twig', [
            'contactsList' => $contactsList,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['contactsList_list' => $this->title],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'contactsList_delete', methods: ['POST'])]
    public function delete(Request $request, ContactsList $contactsList, ContactsListHistoryRepository $contactsListHistoryRepository, CommentsRepository $commentsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $contactsList->getId(), $request->request->get('_token'))) {
            $log = [];
            $log['name'] = 'ContactsList';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Usunięcie ContactsList ' . $contactsList->getTitle();

            // TODO Komentarze
            /*$result = $commentsRepository->findBy(['contactsList' => $contactsList]);
            if ($result) {
                foreach ($result as $comment) {
                    $commentsRepository->remove($comment, true);
                }
            }*/

            // TODO Historia
            /*$result = $contactsListHistoryRepository->findBy(['contactsList' => $contactsList]);
            if ($result) {
                foreach ($result as $comment) {
                    $contactsListHistoryRepository->remove($comment, true);
                }
            }*/

            $this->contactsList->remove($contactsList, true);

            $this->logs->add($log);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('contactsList_list', [], Response::HTTP_SEE_OTHER);
    }

    // TODO Dodawanie plików
    /* #[Route('/deleteFile/{id}', name: 'contactsList_deleteFile', methods: ['GET'])]
        public function deleteFile(Request $request, Files $files): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $id = $files->getContactsList()->getId();

            $this->files->deleteFileById($files->getId());

            $files->getCompanies()->removeFile($files);

            $this->addFlash('success', 'Plik usunięty');

            return $this->redirectToRoute('ContactsList_edit', ['id' => $id], Response::HTTP_SEE_OTHER);
        }*/

}
