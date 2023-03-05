<?php

namespace App\Controller\Core\Tasks;

use App\Entity\Core\Tasks\TasksStatus;
use App\Form\Core\Tasks\TasksStatusType;
use App\Repository\Core\Tasks\TasksStatusRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/zadania/statusy')]
class TasksStatusController extends AbstractController
{
    private TasksStatusRepository $tasks;
    private $buttons;
    private HelperService $helper;

    public function __construct(HelperService $helperService, TasksStatusRepository $tasks)
    {
        $this->tasks = $tasks;
        $this->helper = $helperService;
        $this->buttons = [];

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);
    }

    #[Route('/', name: 'tasksStatus_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-add-circle-fill',
            'button_link' => 'tasksStatus_new',
            'button_title' => 'Dodaj wpis',
        ]]);

        return $this->render('tasks/status/list.html.twig', [
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/json', name: 'tasksStatus_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->tasks->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['value'] = $item->getValue();
                $dat['name'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('tasksStatus_edit', ['id' => $item->getId()]).'" class="badge btn-primary text-white">Edytuj</a>';

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

    #[Route('/nowy-status', name: 'tasksStatus_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $tasksStatus = new TasksStatus();
        $form = $this->createForm(TasksStatusType::class, $tasksStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tasks->save($tasksStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('tasksStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-success',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'tasksStatus_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->renderForm('tasks/status/new.html.twig', [
            'tasks_status' => $tasksStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}/edytuj-status', name: 'tasksStatus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TasksStatus $tasksStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $form = $this->createForm(TasksStatusType::class, $tasksStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tasks->save($tasksStatus, true);

            $this->addFlash('success', 'Wpis zapisany');

            return $this->redirectToRoute('tasksStatus_list', [], Response::HTTP_SEE_OTHER);
        }

        // Buttons

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'tasksStatusVariants_list',
            'button_param' => ['id' => $tasksStatus->getId()],
            'button_title' => 'Zależności: '.$tasksStatus->getVariants()->count(),
        ]]);

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'tasksStatus_list',
            'button_title' => 'Powrót do listy', 'button_mobile_title' => '',
        ]]);

        return $this->renderForm('tasks/status/edit.html.twig', [
            'tasks_status' => $tasksStatus,
            'form' => $form,
            'buttons' => $this->buttons,
            'breadcrumb' => [
                ['tasks_list' => 'Szablony Email'],
                ['none' => 'Status'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'tasksStatus_delete', methods: ['POST'])]
    public function delete(Request $request, TasksStatus $tasksStatus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$tasksStatus->getId(), $request->request->get('_token'))) {
            $this->tasks->remove($tasksStatus, true);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('tasksStatus_list', [], Response::HTTP_SEE_OTHER);
    }
}
