<?php

namespace App\Controller\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsList;
use App\Repository\Other\ContactsList\ContactsListRepository;
use App\Service\CommentService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/contactsList")]
class ContactsListCommentsController extends AbstractController
{

    private $comment;
    private $validation;
    private $contactsList;

    public function __construct(CommentService $commentService, ValidationService $validationService, ContactsListRepository $contactsListRepository)
    {
        $this->comment = $commentService;
        $this->validation = $validationService;
        $this->contactsList = $contactsListRepository;
    }

    #[Route("/komentarze", name: "contactsList_comments")]
    public function comments(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

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

    #[Route("/getComments/{id}", name: "contactsList_getComments", methods: ["GET"])]
    public function getComments(ContactsList $contactsList): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->getComments($contactsList, 'contactsList'));
    }

    #[Route("/addNewComment/{id}", name: "contactsList_addNewComment",methods: ['GET','POST'])]
    public function addNewComment(Request $request, ContactsList $contactsList): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->addNewComment($contactsList, 'contactsList'));
    }

    #[Route("/addNewComment/{parentId}", name: "contactsList_addNewCommentUnderParent",methods: ['GET','POST'])]
    public function addNewCommentUnderParent($parentId): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->addNewCommentUnderParent($parentId));
    }

    #[Route("/deleteComment", name: "contactsList_deleteComment", methods: ["POST"])]
    public function deleteComment(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->deleteComment());
    }
}
