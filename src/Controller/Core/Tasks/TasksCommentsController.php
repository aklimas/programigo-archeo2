<?php

namespace App\Controller\Core\Tasks;

use App\Entity\Core\Tasks\Tasks;
use App\Repository\Core\Tasks\TasksRepository;
use App\Service\CommentService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks')]
class TasksCommentsController extends AbstractController
{
    private CommentService $comment;
    private ValidationService $validation;
    private TasksRepository $tasks;

    public function __construct(CommentService $commentService, ValidationService $validationService, TasksRepository $tasksRepository)
    {
        $this->comment = $commentService;
        $this->validation = $validationService;
        $this->tasks = $tasksRepository;
    }

    #[Route('/komentarze', name: 'tasks_comments')]
    public function comments(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('panel/test/comments/index.html.twig', [
            'buttons' => [
                [
                    'button_class' => 'btn-secondary',
                    'button_icon' => 'fa-arrow-left',
                    'button_link' => 'panel',
                    'button_title' => 'Powrót',
                ],
            ],
        ]);
    }

    #[Route('/getComments/{id}', name: 'tasks_getComments', methods: ['GET'])]
    public function getComments(Tasks $tasks): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->getComments($tasks, 'tasks'));
    }

    #[Route('/addNewComment/{id}', name: 'tasks_addNewComment', methods: ['GET', 'POST'])]
    public function addNewComment(Request $request, Tasks $tasks): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->addNewComment($tasks, 'tasks'));
    }

    #[Route('/addNewComment/{parentId}', name: 'tasks_addNewCommentUnderParent', methods: ['GET', 'POST'])]
    public function addNewCommentUnderParent($parentId): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->addNewCommentUnderParent($parentId));
    }

    #[Route('/deleteComment', name: 'tasks_deleteComment', methods: ['POST'])]
    public function deleteComment(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->deleteComment());
    }
}
