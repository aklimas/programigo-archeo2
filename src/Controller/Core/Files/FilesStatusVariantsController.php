<?php

namespace App\Controller\Core\Files;

use App\Entity\Core\Files\FilesStatus;
use App\Entity\Core\Files\FilesStatusVariants;
use App\Form\Core\Files\FilesStatusVariantsType;
use App\Repository\Core\Files\FilesStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/files/status/variants')]
class FilesStatusVariantsController extends AbstractController
{
    public function __construct(
        private FilesStatusVariantsRepository $files,
        private $buttons = [],
        private $title = 'Files'
    ) {
        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/{id}', name: 'filesStatusVariants_list')]
    public function list(FilesStatus $filesStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'filesStatus_edit',
            'button_param' => ['id' => $filesStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'filesStatusVariants_new',
            'button_param' => ['id' => $filesStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('core/files/status/variants/list.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $filesStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Lista'],
            ],
            'filesStatus' => $filesStatus,
        ]);
    }

    #[Route('/{id}/json/', name: 'filesStatusVariants_jsonList', methods: ['GET'])]
    public function jsonList(FilesStatus $filesStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->files->findBy(['status' => $filesStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('filesStatusVariants_edit', ['status' => $filesStatus->getId(), 'id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/{id}/nowy', name: 'filesStatusVariants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FilesStatus $filesStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $filesStatusVariants = new FilesStatusVariants();
        $form = $this->createForm(FilesStatusVariantsType::class, $filesStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filesStatusVariants->setStatus($filesStatus);
            $this->files->save($filesStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('filesStatusVariants_list', ['id' => $filesStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'filesStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/status/variants/new.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'files_status' => $filesStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $filesStatus->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Nowy'],
            ],
        ]);
    }

    #[Route('/edytuj/{status}/{id}/', name: 'filesStatusVariants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FilesStatus $status, FilesStatusVariants $filesStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(FilesStatusVariantsType::class, $filesStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->files->save($filesStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('filesStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'filesStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/files/status/variants/edit.html.twig', [
            'title' => $this->title,
            'header_title' => $this->title,
            'files_status_variants' => $filesStatusVariants,
            'files_status' => $status,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['files_list' => $this->title],
                ['none' => 'Status'],
                ['none' => $status->getName()],
                ['none' => 'Warianty'],
                ['none' => 'Edycja'],
            ],
        ]);
    }

    #[Route('/delete/{status}/{id}', name: 'filesStatusVariants_delete', methods: ['POST'])]
    public function delete(Request $request, FilesStatus $status, FilesStatusVariants $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->request->get('_token'))) {
            $this->files->remove($id, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('filesStatusVariants_list', ['id' => $status->getId()], Response::HTTP_SEE_OTHER);
    }
}
