<?php

namespace App\Controller\Core\Tasks;

use App\Entity\Core\Tasks\TasksStatus;
use App\Entity\Core\Tasks\TasksStatusVariants;
use App\Form\Core\Tasks\TasksStatusType;
use App\Form\Core\Tasks\TasksStatusVariantsType;
use App\Repository\Core\Tasks\TasksStatusVariantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks/status/variants')]
class TasksStatusVariantsController extends AbstractController
{
    private TasksStatusVariantsRepository $tasks;
    private $buttons;

    public function __construct(TasksStatusVariantsRepository $tasks)
    {
        $this->tasks = $tasks;
        $this->buttons = [];

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/{id}', name: 'tasksStatusVariants_list')]
    public function list(TasksStatus $tasksStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'tasksStatus_edit',
            'button_param' => ['id' => $tasksStatus->getId()],
            'button_title' => 'Powrót do statusu',
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'tasksStatusVariants_new',
            'button_param' => ['id' => $tasksStatus->getId()],
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('tasks/status/variants/list.html.twig', [
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
                ['none' => $tasksStatus->getName()],
                ['none' => 'Warianty'],
            ],
            'worklowStatus' => $tasksStatus,
        ]);
    }

    #[Route('/{id}/json/', name: 'tasksStatusVariants_jsonList', methods: ['GET'])]
    public function jsonList(TasksStatus $tasksStatus, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->tasks->findBy(['status' => $tasksStatus]);
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['role'] = $item->getRole();
                $dat['name'] = $item->getName();
                $dat['actions'] = '<a href="'.$this->generateUrl('tasksStatusVariants_edit', ['status' => $tasksStatus->getId(), 'id' => $item->getId()]).'" class="badge btn-primary text-white">Edytuj</a>';

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

    #[Route('/{id}/nowy', name: 'tasksStatusVariants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TasksStatus $tasksStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $tasksStatusVariants = new TasksStatusVariants();
        $form = $this->createForm(TasksStatusVariantsType::class, $tasksStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tasksStatusVariants->setStatus($tasksStatus);
            $this->tasks->save($tasksStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('tasksStatusVariants_list', ['id' => $tasksStatus->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'tasksStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('tasks/status/variants/new.html.twig', [
            'tasks_status' => $tasksStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{status}/{id}/edytuj', name: 'tasksStatusVariants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TasksStatus $tasksStatus, TasksStatusVariants $tasksStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(TasksStatusType::class, $tasksStatusVariants);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tasks->save($tasksStatusVariants, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('tasksStatusVariants_list', [], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'tasksStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('tasks/status/variants/edit.html.twig', [
            'tasks_status' => $tasksStatusVariants,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'tasksStatus_delete', methods: ['POST'])]
    public function delete(Request $request, TasksStatusVariants $tasksStatusVariants): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$tasksStatusVariants->getId(), $request->request->get('_token'))) {
            $this->tasks->remove($tasksStatusVariants, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('tasksStatusVariants_list', [], Response::HTTP_SEE_OTHER);
    }
}
