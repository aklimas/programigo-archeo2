<?php

namespace App\Twig\Messenger;

use App\Service\Messenger\MessengerService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessengerExtension extends AbstractExtension
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sendMessage', [$this, 'sendMessage']),
            new TwigFunction('countNewMessages', [$this, 'countNewMessages']),
            new TwigFunction('getMessages', [$this, 'getMessages']),
            new TwigFunction('getMessagesSended', [$this, 'getMessagesSended']),
            new TwigFunction('readMessage', [$this, 'readMessage']),
            new TwigFunction('deleteMessage', [$this, 'deleteMessage']),
            new TwigFunction('formMessage', [$this, 'formMessage']),
        ];
    }

    public function sendMessage(): bool
    {
        return $this->messengerService->sendMessage();
    }

    public function getMessages($limit = null): array
    {
        return $this->messengerService->getMessages($limit);
    }

    public function getMessagesSended($limit = null): array
    {
        return $this->messengerService->getMessagesSended($limit);
    }

    public function countNewMessages(): int
    {
        return $this->messengerService->countNewMessages();
    }

    public function readMessage($id): bool
    {
        return $this->messengerService->readMessage($id);
    }

    public function deleteMessage(): bool
    {
        return $this->messengerService->deleteMessage();
    }

    public function formMessage()
    {
        return $this->messengerService->newMessage();
    }
}
