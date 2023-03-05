<?php

namespace App\Controller\Core\Files;

use App\Entity\Core\Files\Files;
use App\Repository\Core\Files\FilesRepository;
use App\Service\CommentService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/files')]
class FilesCommentsController extends AbstractController
{

    public function __construct(private CommentService $comment)
    {
    }

    #[Route('/komentarze', name: 'files_comments')]
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

    #[Route('/getComments/{id}', name: 'files_getComments', methods: ['GET'])]
    public function getComments(Files $files): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->getComments($files, 'files'));
    }

    #[Route('/addNewComment/{id}', name: 'files_addNewComment', methods: ['GET', 'POST'])]
    public function addNewComment(Request $request, Files $files): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->addNewComment($files, 'files'));
    }

    #[Route('/addNewComment/{parentId}', name: 'files_addNewCommentUnderParent', methods: ['GET', 'POST'])]
    public function addNewCommentUnderParent($parentId): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->addNewCommentUnderParent($parentId));
    }

    #[Route('/deleteComment', name: 'files_deleteComment', methods: ['POST'])]
    public function deleteComment(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return new JsonResponse($this->comment->deleteComment());
    }
}
