<?php

namespace App\EventSubscriber;

use App\Repository\Core\LogsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(private LogsRepository $logs, private Security $security)
    {
    }

    public function onSecurityAuthenticationSuccess($event): void
    {
        // TODO do zrobienia rejestrowanie zalogowania
        $log = [];
        $log['name'] = 'Logowanie';
        $log['date'] = new \DateTime(); // Data rozpoczęcia zadania

        // $log['user'] = $this->security->getUser(;
        // ...
        $this->logs->add($log);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
        ];
    }
}
