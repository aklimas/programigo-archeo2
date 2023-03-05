<?php

namespace App\Command;

use App\Repository\Core\UserEmailRepository;
use App\Service\EmailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailQueueCommand extends Command
{
    private EmailService $emailService;
    private UserEmailRepository $userEmail;

    /**
     * UpdateProductCommand constructor.
     */
    public function __construct(EmailService $emailService, UserEmailRepository $userEmailRepository)
    {
        parent::__construct();

        $this->emailService = $emailService;
        $this->userEmail = $userEmailRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('email:send')->setDescription('Komenda do wysyłania emaila z crona.');
    }

    public function sortFunction($a, $b)
    {
        return strtotime($a->date_payment) - strtotime($b->date_payment);
    }

    /**
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_email = [];

        $toDay = new \DateTime();
        $output->writeln('Dziś: '.$toDay->format('Y-m-d H:i:s'));

        $emailQueue = $this->emailService->getEmailQueue();
        if ($emailQueue) {
            foreach ($emailQueue as $email) {
                // wysyłam emaila

                $_email = $this->userEmail->findOneBy(['id' => 1, 'user' => 1]);
                $from = $_email->getSenderEmail();
                $from_label = $_email->getSenderLabel();

                // Jeżeli pojedyńczy email to wysyłamy
                if ('single' === $email->getType()) {
                    $return = $this->emailService->sendEmail([
                            'from' => $from,
                            'from_label' => $from_label,

                            'to' => $email->getRecipient(),
                            'to_label' => $email->getRecipientLabel(),
                            'subject' => $email->getSubject(),
                            'message' => $email->getContent(),
                            // 'replyTo' => 'kontakt@appcode.eu',
                            // 'priority' => true,
                            // 'dw' => 'kontakt@appcode.eu',
                            // 'udw' => 'kontakt@appcode.eu',
                            // 'htmlTemplate' => '',
                        ]);

                    $this->emailService->sended($email->getId());
                    sleep(10);

                    $output->writeln('Wysyłamy:  '.$email->getRecipient());
                } else {
                    // jeżeli nie jest single, to szukamy emaili o takim samym typie i tworzymy jedną masową wiadomość

                    $_email[$email->getType()]['recipient'] = $email->getRecipient();
                    $_email[$email->getType()]['recipient_label'] = $email->getRecipientLabel();
                    $_email[$email->getType()]['sender_email'] = $email->getUser()->getUserEmail()->getSenderEmail();
                    $_email[$email->getType()]['sender_label'] = $email->getUser()->getUserEmail()->getSenderLabel();
                    $_email[$email->getType()]['subject'] = $email->getSubject();
                    $_email[$email->getType()]['content'][] = $email->getContent();
                    $_email[$email->getType()]['ids'][] = $email->getId();
                }
            }

            if (!empty($_email)) {
                foreach ($_email as $oneEmail) {
                    $_html = '';
                    foreach ($oneEmail['content'] as $content) {
                        $_html .= $content.'<br>';
                    }

                    $return = $this->emailService->sendEmail([
                        'from' => $oneEmail['sender_email'],
                        'from_label' => $oneEmail['sender_label'],
                        'to' => $oneEmail['recipient'],
                        'to_label' => $oneEmail['recipient_label'],
                        'subject' => $oneEmail['subject'],
                        'message' => $_html,
                        // 'replyTo' => 'kontakt@appcode.eu',
                        // 'priority' => true,
                        // 'dw' => 'kontakt@appcode.eu',
                        // 'udw' => 'kontakt@appcode.eu',
                        // 'htmlTemplate' => '',
                    ]);

                    if ($return) {
                        foreach ($oneEmail['ids'] as $id) {
                            $this->emailService->sended($id);
                        }
                    }

                    sleep(10);
                }
            }
        } else {
            $output->writeln('Brak emaili do wysłania');
        }

        return Command::SUCCESS;
    }
}
