<?php

namespace App\Controller\Core\Files;

use App\Entity\Core\Files\FilesStatus;
use App\Form\Core\Files\FilesStatusType;
use App\Repository\Core\Files\FilesStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pliki/statusy')]
class FilesStatusController extends AbstractController
{
    public function __construct(
        private HelperService $helper,
        private FilesStatusRepository $files,
        private $buttons = [],
        private string $title = 'Files')
    {
        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/', name: 'filesStatus_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'filesStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('core/files/status/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Lista'],
            ],
        ]);
    }

    #[Route('/json', name: 'filesStatus_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->files->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('filesStatus_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/nowy-status', name: 'filesStatus_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $filesStatus = new FilesStatus();
        $form = $this->createForm(FilesStatusType::class, $filesStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->files->save($filesStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('filesStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'filesStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/status/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'files_status' => $filesStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj-status', name: 'filesStatus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FilesStatus $filesStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(FilesStatusType::class, $filesStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->files->save($filesStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('filesStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'filesStatusVariants_list',
            'button_param' => ['id' => $filesStatus->getId()],
            'button_title' => 'Zależności: '.$filesStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'filesStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/status/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'files_status' => $filesStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Statusy'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'filesStatus_delete', methods: ['POST'])]
    public function delete(Request $request, FilesStatus $filesStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$filesStatus->getId(), $request->request->get('_token'))) {
            $this->files->remove($filesStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('filesStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
