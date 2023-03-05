<?php

namespace App\Controller\Core;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tests')]
class TestsController extends AbstractController
{
    public function __construct(private EmailService $emailService)
    {
    }

    #[Route('/email', name: 'tests_email')]
    public function index(): Response
    {
        $return = $this->emailService->sendEmail([
            'to' => 'dev@programigo.com',
            'to_label' => 'Programigo test',
            'subject' => 'Subject',
            'message' => 'Message',
            'htmlTemplate' => '/_modules/email/empty.html.twig',
        ]);

        return new JsonResponse($return);
    }
}
