<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    #[Route('/app/files', name: 'app_files')]
    public function index(): Response
    {
        return $this->render('app/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }
}
