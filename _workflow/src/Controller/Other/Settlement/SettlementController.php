<?php

namespace App\Controller\Other\Settlement;

use App\Entity\Core\Files\Files;
use App\Entity\Other\Settlement\Settlement;
use App\Entity\Other\Settlement\SettlementHistory;
use App\Form\Other\Settlement\SettlementType;
use App\Repository\Core\CommentsRepository;
use App\Repository\Core\LogsRepository;
use App\Repository\Other\Settlement\SettlementHistoryRepository;
use App\Repository\Other\Settlement\SettlementRepository;
use App\Service\FilesService;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/settlement')]
class SettlementController extends AbstractController
{
    public function __construct(
        private HelperService     $helper,
        private SettlementRepository $settlement,
        // TODO Historia
        //private SettlementHistoryRepository $settlementHistory,
        private Security          $security,
        // TODO Pliki
        //private FilesService $files,
        private LogsRepository    $logs,
        private string            $title = 'Settlement',
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
            'button_link' => 'settlement_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'settlementStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'settlement_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('other/settlement/list.html.twig', [
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Lista'],
            ],
        ]);
    }

    #[Route('/json', name: 'settlement_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->settlement->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['title'] = $item->getTitle();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                $dat['dateModify'] = $item->getDateModify()->format('d-m-Y H:i');
                // TOTO Status
                //$dat['status'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="' . $this->generateUrl('settlement_edit', ['id' => $item->getId()]) . '" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/nowy', name: 'settlement_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $settlement = new Settlement();
        $form = $this->createForm(SettlementType::class, $settlement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Settlement';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Dodano nowy Settlement';

            // TODO Dodawanie plików
            /*$_file = $form->get('file')->getData();
            if (null !== $_file and count($_file) > 1) {
                foreach ($_file as $f) {
                    $result = $this->files->upload($f);
                    $file = $this->files->getFile($result);
                    $settlement->addFile($file);
                }
            } elseif (null !== $_file and count($_file) === 1) {
                $result = $this->files->upload($_file);
                $file = $this->files->getFile($result);
                $settlement->addFile($file);
            }*/

            $this->settlement->save($settlement, true);

            // TODO Historia
            /*$settlementHistory = new SettlementHistory();
            $settlementHistory->setValue('Utworzenie settlement');
            $settlementHistory->setSettlement($settlement);
            $this->settlementHistory->save($settlementHistory, true);*/

            $this->addFlash('success', 'Wpis zapisany');

            $this->logs->add($log);

            return $this->redirectToRoute('settlement_edit', ['id' => $settlement->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'settlement_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/new.html.twig', [
            'settlement' => $settlement,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj', name: 'settlement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Settlement $settlement): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // TOTO Status
        //$oldStatus = $settlement->getStatus();

        $form = $this->createForm(SettlementType::class, $settlement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Settlement';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Edycja Settlement ' . $settlement->getTitle();

            // TODO Dodawanie plików
            /*$_file = $form->get('file')->getData();
            if (null !== $_file and count($_file) > 1) {
                foreach ($_file as $f) {
                    $result = $this->files->upload($f);
                    $file = $this->files->getFile($result);
                    $settlement->addFile($file);
                }
            } elseif (null !== $_file and count($_file) === 1) {
                $result = $this->files->upload($_file);
                $file = $this->files->getFile($result);
                $settlement->addFile($file);
            }*/

            $this->settlement->save($settlement, true);

            // TODO Historia
            /*if ($oldStatus !== $settlement->getStatus()) {
                $statusMessage = 'Status: '.$oldStatus->getName().' > '.$settlement->getStatus()->getName();
                $settlementHistory = new SettlementHistory();
                $settlementHistory->setValue($statusMessage);
                $settlementHistory->setSettlement($settlement);
                $this->settlementHistory->save($settlementHistory, true);
            }*/

            $this->logs->add($log);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('settlement_edit', ['id' => $settlement->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'settlement_list',
            'button_title' => 'Powrót do listy',
        ]]);

        return $this->renderForm('other/settlement/edit.html.twig', [
            'settlement' => $settlement,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['settlement_list' => $this->title],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'settlement_delete', methods: ['POST'])]
    public function delete(Request $request, Settlement $settlement, SettlementHistoryRepository $settlementHistoryRepository, CommentsRepository $commentsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $settlement->getId(), $request->request->get('_token'))) {
            $log = [];
            $log['name'] = 'Settlement';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Usunięcie Settlement ' . $settlement->getTitle();

            // TODO Komentarze
            /*$result = $commentsRepository->findBy(['settlement' => $settlement]);
            if ($result) {
                foreach ($result as $comment) {
                    $commentsRepository->remove($comment, true);
                }
            }*/

            // TODO Historia
            /*$result = $settlementHistoryRepository->findBy(['settlement' => $settlement]);
            if ($result) {
                foreach ($result as $comment) {
                    $settlementHistoryRepository->remove($comment, true);
                }
            }*/

            $this->settlement->remove($settlement, true);

            $this->logs->add($log);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('settlement_list', [], Response::HTTP_SEE_OTHER);
    }

    // TODO Dodawanie plików
    /* #[Route('/deleteFile/{id}', name: 'settlement_deleteFile', methods: ['GET'])]
        public function deleteFile(Request $request, Files $files): Response
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $id = $files->getSettlement()->getId();

            $this->files->deleteFileById($files->getId());

            $files->getCompanies()->removeFile($files);

            $this->addFlash('success', 'Plik usunięty');

            return $this->redirectToRoute('Settlement_edit', ['id' => $id], Response::HTTP_SEE_OTHER);
        }*/

}
