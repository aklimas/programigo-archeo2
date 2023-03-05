<?php

namespace App\Controller\Core\Messenger;

use App\Entity\Core\Messenger\Messenger;
use App\Form\Core\Messenger\NewMessageType;
use App\Service\Messenger\MessengerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
    public function __construct(
        private MessengerService $message)
    {
    }

    #[Route('/modal', name: 'messagesJson', methods: ['GET', 'POST'])]
    public function modal(Request $request): bool|JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isXmlHttpRequest()) {
            $json['templateShort'] = json_encode($this->render('_modules/_messages/_messagesBodyShort.html.twig')->getContent());
            $json['templateReceived'] = json_encode($this->render('_modules/_messages/_messagesBodyReceived.html.twig')->getContent());
            $json['templateSent'] = json_encode($this->render('_modules/_messages/_messagesBodySent.html.twig')->getContent());

            $response = new JsonResponse($json);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return false;
    }

    #[Route('/komunikator/json', name: 'messenger_listJson', methods: ['GET', 'POST'])]
    public function getMessageJson(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data['value'] = $this->message->getMessages();
        // $data['value'] = ['message' => 'test'];
        return new JsonResponse($data);
    }

    #[Route('/komunikator', name: 'messenger_list', methods: ['GET', 'POST'])]
    public function list(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $newMessage = $this->message->newMessage();
        if (true === $newMessage['status']) {
            return $this->redirectToRoute('messenger_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('core/messenger/list.html.twig', [
            'formMessage' => $newMessage['form'],
        ]);
    }

    #[Route('/komunikator/odczytaj/{id}', name: 'messenger_read', methods: ['POST'])]
    public function readMessage(Messenger $messenger): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $this->message->readMessage($messenger);

        return new JsonResponse('true');
    }

    #[Route('/komunikator/wyslij/{id}', name: 'messenger_send', methods: ['POST'])]
    public function sendMessage(Messenger $messenger, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $messenger->setResponse(true);
            $this->message->repository()->save($messenger, true);

            $content = $request->request->get('content');

            $this->message->sendMessage($content, $messenger->getUserSend());

            return new JsonResponse('true');
        } else {
            return new JsonResponse('false');
        }
    }

    #[Route('/komunikator/nowa-wiadomosc', name: 'messenger_newMessage', methods: ['POST'])]
    public function newMessage(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isXmlHttpRequest() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $message = new Messenger();
            $form = $this->createForm(NewMessageType::class, $message);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $message->setUserSend($this->getUser());
                $this->message->repository()->save($message, true);
            }

            return new JsonResponse('true');
        } else {
            return new JsonResponse('false');
        }
    }
}
