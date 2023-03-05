<?php

namespace App\Service;

use App\Repository\Core\EmailQueueRepository;
use App\Repository\Core\LogsRepository;
use App\Repository\Core\SettingsRepository;
use App\Repository\Core\UserEmailRepository;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class EmailService
{
    private SettingsRepository $settings;
    private UserEmailRepository $userEmail;
    private LogsRepository $logs;
    private HelperService $helper;

    public function __construct(
        SettingsRepository $settingsRepository,
        UserEmailRepository $userEmailRepository,
        LogsRepository $logsRepository,
        HelperService $helperService,
        private EmailQueueRepository $emailQueue
    ) {
        $this->settings = $settingsRepository;
        $this->userEmail = $userEmailRepository;
        $this->logs = $logsRepository;
        $this->helper = $helperService;
    }

    public function sendEmail($args)
    {
        try {
            if (!isset($args['privateSender'])) {
                $_email = $this->userEmail->findOneBy(['id' => 1, 'user' => 1]);
            } else {
                $_email = $this->userEmail->findOneBy(['user' => $args['privateSender']]);
            }

            $user = $_email->getSenderEmail();
            $pass = $this->helper->decode($_email->getSenderPass());
            $server = $_email->getSenderHost();
            $label = $_email->getSenderLabel();

            $args['from'] = $user;
            $args['from_label'] = $label;

            $dsn = 'smtp://'.$user.':'.$pass.'@'.$server.'?verify_peer=0';
            $transport = Transport::fromDsn($dsn);
            $customMailer = new Mailer($transport);

            // weryfikacja danych

            if (!isset($args['to'])) {
                throw new NotFoundHttpException('Uzupełnij pole TO (Do)');
            }
            if (!isset($args['logo'])) {
                $logo = $this->settings->get('logo', true);
                $args['logo'] = $logo->getPath().$logo->getName();
            }
            if (!isset($args['to_label'])) {
                throw new NotFoundHttpException('Uzupełnij pole TO (DO Nazwa)');
            }

            if (!isset($args['subject'])) {
                throw new NotFoundHttpException('Uzupełnij temat emaila');
            }
            // if (!isset($args['message'])) throw new NotFoundHttpException('Uzupełnij treść emaila');
            if (!isset($args['dw'])) {
                $args['dw'] = null;
            }
            if (!isset($args['udw'])) {
                $args['udw'] = null;
            }
            if (!isset($args['replyTo'])) {
                $args['replyTo'] = null;
            }
            if (!isset($args['priority'])) {
                $args['priority'] = null;
            }

            if (!isset($args['htmlTemplate'])) {
                $args['htmlTemplate'] = '/_modules/email/template.html.twig';
            }

            $context['logo'] = '/_modules/email/'.$args['logo'];
            $context['subject'] = $args['subject'];
            if (isset($args['message'])) {
                $context['message'] = $args['message'];
            }

            if (isset($args['context'])) {
                $context = $context + $args['context'];
            }

            $email = (new TemplatedEmail())
                ->from(new Address($args['from'], $args['from_label']))
                ->to(new Address($args['to'], $args['to_label']))
                ->subject($args['subject'])
                ->htmlTemplate($args['htmlTemplate'])
                ->context($context);

            if (isset($args['attach']) and null != $args['attach']) {
                if (is_array($args['attach'])) {
                    foreach ($args['attach'] as $attach) {
                        $email->attachFromPath($attach);
                    }
                } else {
                    $email->attachFromPath($args['attach']);
                }
            }
            if (null != $args['dw']) {
                $email->cc($args['dw']);
            }
            if (null != $args['udw']) {
                $email->bcc($args['udw']);
            }
            if (null != $args['replyTo']) {
                $email->replyTo($args['replyTo']);
            }
            if (true == $args['priority']) {
                $email->priority(Email::PRIORITY_HIGH);
            }

            // $transport = Transport::fromDsn('smtp://symfony@programigo.com:1IMXKC8nR@c1896.lh.pl?verify_peer=0');
            // $mailer = new Mailer($transport);

            // IMPORTANT: as you are using a customized mailer instance, you have to make the following
            // configuration as indicated in https://github.com/symfony/symfony/issues/35990.
            $loader = new FilesystemLoader(__DIR__.'/../../templates/');
            $twigEnv = new Environment($loader);
            $twigBodyRenderer = new BodyRenderer($twigEnv);
            $twigBodyRenderer->render($email);

            $customMailer->send($email);

            return true;
        } catch (TransportExceptionInterface  $e) {
            $log = [];
            $log['name'] = 'Email';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = $e->getTrace();
            $log['status'] = 'ERROR';
            $log['date_end'] = new \DateTime(); // Data zakończenia zadania
            $this->logs->add($log);

            return $e->getTrace();
        }
    }

    public function getEmailQueue()
    {
        $result = $this->emailQueue->findBy(['status' => 0]);

        return $result;
    }

    public function sended($id)
    {
        $email = $this->emailQueue->findOneBy(['id' => $id]);
        $email->setStatus('sent');
        $email->setDatasend(new \DateTime());

        $this->emailQueue->save($email, true);

        return true;
    }
}
