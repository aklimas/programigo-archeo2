<?php

namespace App\Controller\Core\Tasks;

use App\Entity\Core\Tasks\Tasks;
use App\Entity\Core\Tasks\TasksHistory;
use App\Form\Core\Tasks\TasksType;
use App\Repository\Core\CommentsRepository;
use App\Repository\Core\LogsRepository;
use App\Repository\Core\Tasks\TasksHistoryRepository;
use App\Repository\Core\Tasks\TasksRepository;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/zadania')]
class TasksController extends AbstractController
{
    private TasksRepository $tasks;
    private $buttons;
    private TasksHistoryRepository $tasksHistory;
    private LogsRepository $logs;
    private HelperService $helper;
    private string $title = 'Zadania';

    public function __construct(
        HelperService $helperService,
        TasksRepository $tasks,
        TasksHistoryRepository $tasksHistoryRepository,
        Security $security,
        LogsRepository $logsRepository
    ) {
        $this->tasks = $tasks;
        $this->helper = $helperService;
        $this->tasksHistory = $tasksHistoryRepository;
        $this->logs = $logsRepository;
        $this->buttons = [];

        // Buttons
        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary float-end ms-1',
            'button_icon' => 'ri-home-2-fill',
            'button_link' => 'home',
            'button_title' => '',
        ]]);

        if ($security->isGranted('ROLE_SUPER_ADMIN')) {
            $this->buttons = array_merge($this->buttons, [[
                'button_class' => 'btn-info',
                'button_icon' => 'ri-file-list-3-fill',
                'button_link' => 'tasksStatus_list',
                'button_title' => 'Statusy',
            ]]);
        }
    }

    #[Route('/', name: 'tasks_list')]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('core/tasks/list.html.twig', [
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['tasks_list' => $this->title],
            ],
        ]);
    }

    #[Route('/json', name: 'tasks_jsonList', methods: ['GET'])]
    public function jsonList(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $collection = $this->tasks->findAll();
            $data = [];

            foreach ($collection as $item) {
                $dat = [];
                $dat['id'] = $item->getId();
                $dat['title'] = $item->getTitle();
                $dat['dateAdd'] = $item->getDateAdd()->format('d-m-Y H:i');
                $dat['dateModify'] = $item->getDateModify()->format('d-m-Y H:i');
                $dat['status'] = $this->helper->status($item, true);
                $dat['actions'] = '<a href="'.$this->generateUrl('tasks_edit', ['id' => $item->getId()]).'" class="badge btn btn-primary text-white">Edytuj</a>';

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

    #[Route('/nowe-zadanie', name: 'tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $tasks = new Tasks();
        $form = $this->createForm(TasksType::class, $tasks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $log = [];
            $log['name'] = 'Tasks';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Dodano nowy Tasks';

            $this->tasks->save($tasks, true);

            $tasksHistory = new TasksHistory();
            $tasksHistory->setValue('Utworzenie tasks');
            $tasksHistory->setTasks($tasks);
            $this->tasksHistory->save($tasksHistory, true);

            $this->addFlash('success', 'Wpis zapisany');

            $log['status'] = 'SUCCESS';
            $log['date_end'] = new \DateTime(); // Data zakończenia zadania
            $this->logs->add($log);

            return $this->redirectToRoute('tasks_edit', ['id' => $tasks->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'tasks_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->renderForm('core/tasks/new.html.twig', [
            'tasks' => $tasks,
            'form' => $form,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['tasks_list' => $this->title],
            ],
        ]);
    }

    #[Route('/{id}/edytuj-zadanie', name: 'tasks_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tasks $tasks): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->buttons = array_merge($this->buttons, [[
            'button_class' => 'btn-secondary',
            'button_icon' => 'ri-file-list-3-fill',
            'button_link' => 'tasks_list',
            'button_title' => 'Powrót do listy',
            'button_mobile_title' => '',
        ]]);

        return $this->render('core/tasks/edit.html.twig', [
            'tasks' => $tasks,
            'buttons' => $this->buttons,
            'title' => $this->title,
            'header_title' => $this->title,
            'breadcrumb' => [
                ['tasks_list' => $this->title],
            ],
        ]);
    }

    #[Route('/{id}', name: 'tasks_delete', methods: ['POST'])]
    public function delete(Request $request, Tasks $tasks, TasksHistoryRepository $tasksHistoryRepository, CommentsRepository $commentsRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$tasks->getId(), $request->request->get('_token'))) {
            $log = [];
            $log['name'] = 'Tasks';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = 'Usunięcie Tasks '.$tasks->getTitle();

            $result = $commentsRepository->findBy(['tasks' => $tasks]);
            if ($result) {
                foreach ($result as $comment) {
                    $commentsRepository->remove($comment, true);
                }
            }

            $result = $tasksHistoryRepository->findBy(['tasks' => $tasks]);
            if ($result) {
                foreach ($result as $comment) {
                    $tasksHistoryRepository->remove($comment, true);
                }
            }

            $this->tasks->remove($tasks, true);

            $this->logs->add($log);
        }

        $this->addFlash('success', 'Wpis usunięty');

        return $this->redirectToRoute('tasks_list', [], Response::HTTP_SEE_OTHER);
    }
}
