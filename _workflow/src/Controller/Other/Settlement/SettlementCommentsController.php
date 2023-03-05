<?php

namespace App\Controller\Other\Settlement;

use App\Entity\Other\Settlement\Settlement;
use App\Repository\Other\Settlement\SettlementRepository;
use App\Service\CommentService;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/settlement")]
class SettlementCommentsController extends AbstractController
{

    private $comment;
    private $validation;
    private $settlement;

    public function __construct(CommentService $commentService, ValidationService $validationService, SettlementRepository $settlementRepository)
    {
        $this->comment = $commentService;
        $this->validation = $validationService;
        $this->settlement = $settlementRepository;
    }

    #[Route("/komentarze", name: "settlement_comments")]
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

    #[Route("/getComments/{id}", name: "settlement_getComments", methods: ["GET"])]
    public function getComments(Settlement $settlement): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->getComments($settlement, 'settlement'));
    }

    #[Route("/addNewComment/{id}", name: "settlement_addNewComment",methods: ['GET','POST'])]
    public function addNewComment(Request $request, Settlement $settlement): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->addNewComment($settlement, 'settlement'));
    }

    #[Route("/addNewComment/{parentId}", name: "settlement_addNewCommentUnderParent",methods: ['GET','POST'])]
    public function addNewCommentUnderParent($parentId): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->addNewCommentUnderParent($parentId));
    }

    #[Route("/deleteComment", name: "settlement_deleteComment", methods: ["POST"])]
    public function deleteComment(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse($this->comment->deleteComment());
    }
}
