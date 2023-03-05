<?php

namespace App\Service\Messenger;

use App\Entity\Core\Messenger\Messenger;
use App\Form\Core\Messenger\NewMessageType;
use App\Repository\Core\Messenger\MessengerRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class MessengerService
{
    private MessengerRepository $messengerRepository;
    protected Security $security;
    private FormFactoryInterface $formFactory;
    private RequestStack $requestStack;

    private array $array = [];

    public function __construct(
        MessengerRepository $messengerRepository,
        Security $security,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory
    ) {
        $this->messengerRepository = $messengerRepository;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    public function repository()
    {
        return $this->messengerRepository;
    }

    public function sendMessage($content, $user): bool
    {
        $message = new Messenger();
        $message->setContent($content);
        $message->setDateSend(new \DateTime());
        $message->setUserSend($this->security->getUser());
        $message->setUserReceipt($user);
        $this->messengerRepository->save($message, true);

        return true; // false
    }

    public function countNewMessages(): int
    {
        $messages = $this->messengerRepository->findBy(['userReceipt' => $this->security->getUser(), 'dateReceipt' => null]);

        return count($messages);
    }

    public function getMessages($limit = null, $all = false): array
    {
        $isMessages = $this->messengerRepository->findBy(['userReceipt' => $this->security->getUser()], ['dateSend' => 'DESC'], $limit);

        return $isMessages;
    }

    public function getMessagesSended($limit = null, $all = false): array
    {
        $isMessages = $this->messengerRepository->findBy(['userSend' => $this->security->getUser()], ['dateSend' => 'DESC'], $limit);

        return $isMessages;
    }

    public function readMessage($id): bool
    {
        $messages = $this->messengerRepository->findOneBy(['id' => $id]);
        $messages->setDateReceipt(new \DateTime());
        $this->messengerRepository->save($messages, true);

        return true; // false
    }

    public function deleteMessage(): bool
    {
        return true; // false
    }

    public function newMessage(): array
    {
        $return['status'] = false;

        $request = $this->requestStack->getCurrentRequest();
        $message = new Messenger();
        $form = $this->formFactory->create(NewMessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUserSend($this->security->getUser());
            $this->repository()->save($message, true);
            $return['status'] = true;
        }

        $return['form'] = $form->createView();

        return $return;
    }
}
