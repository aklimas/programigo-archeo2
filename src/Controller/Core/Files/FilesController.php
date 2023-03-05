<?php

namespace App\Controller\Core\Files;

use App\Entity\Core\Files\Files;
use App\Entity\Core\Files\FilesHistory;
use App\Form\Core\Files\FilesType;
use App\Repository\Core\CommentsRepository;
use App\Repository\Core\Files\FilesHistoryRepository;
use App\Repository\Core\Files\FilesRepository;
use App\Repository\Core\LogsRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/pliki')]
class FilesController extends AbstractController
{
    public function __construct(
        private HelperService $helper,
        private FilesRepository $files,
        private FilesHistoryRepository $filesHistory,
        private Security $security,
        private LogsRepository $logs,
        private string $title = 'Pliki',
        private $buttons = []
    ) {
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
            'button_link' => 'files_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'filesStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'files_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('core/files/list.html.twig', [
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Lista'],
            ],
        ]);
    }

    #[Route('/json', name: 'files_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->files->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['path'] = $item->getPath();
                $dat['name'] = $item->getName();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                // $dat['dateModify'] = ($item->getDateModify() !== null) ?? $item->getDateModify()->format('d-m-Y H:i');
                $dat['status'] = $this->helper->status($item, true);
                // $dat['actions'] = '<a href="'.$this->generateUrl('files_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/dodaj-plik', name: 'files_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $files = new Files();
        $form = $this->createForm(FilesType::class, $files);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Files';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Dodano nowy Files';

            $this->files->save($files, true);

            $filesHistory = new FilesHistory();
            $filesHistory->setValue('Utworzenie files');
            $filesHistory->setFiles($files);
            $this->filesHistory->save($filesHistory, true);

            $this->addFlash('success', 'Wpis zapisany');

            $this->logs->add($log);

            return $this->redirectToRoute('files_edit', ['id' => $files->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'files_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/new.html.twig', [
            'files' => $files,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj-plik', name: 'files_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Files $files): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $oldStatus = $files->getStatus();

        $form = $this->createForm(FilesType::class, $files);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Files';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Edycja Files '.$files->getTitle();

            $this->files->save($files, true);

            if ($oldStatus !== $files->getStatus()) {
                $statusMessage = 'Status: '.$oldStatus->getName().' > '.$files->getStatus()->getName();
                $filesHistory = new FilesHistory();
                $filesHistory->setValue($statusMessage);
                $filesHistory->setFiles($files);
                $this->filesHistory->save($filesHistory, true);
            }

            $this->logs->add($log);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('files_edit', ['id' => $files->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'files_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/edit.html.twig', [
            'files' => $files,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'files_delete', methods: ['POST'])]
    public function delete(Request $request, Files $files, FilesHistoryRepository $filesHistoryRepository, CommentsRepository $commentsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$files->getId(), $request->request->get('_token'))) {
            $log = [];
            $log['name'] = 'Files';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Usunięcie Files '.$files->getTitle();

            $result = $commentsRepository->findBy(['files' => $files]);
            if ($result) {
                foreach ($result as $comment) {
                    $commentsRepository->remove($comment, true);
                }
            }

            $result = $filesHistoryRepository->findBy(['files' => $files]);
            if ($result) {
                foreach ($result as $comment) {
                    $filesHistoryRepository->remove($comment, true);
                }
            }

            $this->files->remove($files, true);

            $this->logs->add($log);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('files_list', [], Response::HTTP_SEE_OTHER);
    }
}
