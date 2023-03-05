<?php

namespace App\Service\Email;

use App\Entity\Core\EmailQueue;
use App\Repository\Core\EmailQueueRepository;
use App\Repository\Other\Campaigns\CampaignsRepository;
use App\Repository\Other\ContactsList\ContactsRepository;
use App\Service\DateService;

class EmailQueueService
{
    public function __construct(
        private EmailQueueRepository $emailQueue,
        private DateService $date)
    {
    }

    /**
     * @throws \Exception
     */
    public function countPostEmail($from = null, $to = null): int
    {
        if (null === $from) {
            $from = new \DateTime('2022-08-14');
        }
        if (null === $to) {
            $to = $this->date->addOneMonth($from);
        }

        $result = $this->emailQueue->findByDate($from, $to);

        return count($result);
    }

    public function countNoPostEmail($from, $to): int
    {
        $result = $this->emailQueue->findByDate($from, $to, 0);

        return count($result);
    }

    public function addEmail($args)
    {
        $newEmail = new EmailQueue();
        $newEmail->setSubject($args['subject']);
        $newEmail->setContent($args['content']);
        $newEmail->setRecipient($args['recipient']);
        $newEmail->setRecipientLabel($args['recipientLabel'] ?? '');
        $newEmail->setPostPlanDate($args['postPlanDate']);
        $newEmail->setSenderEmail($args['senderEmail'] ?? null);
        $newEmail->setSenderLabel($args['senderLabel'] ?? null);
        $newEmail->setUser($args['user'] ?? null);
        $newEmail->setType($args['type']);
        $newEmail->setCreateDate(new \DateTime());

        if (isset($args['template'])) {
            $newEmail->setTemplate($args['template']);
        }

        $this->emailQueue->save($newEmail, true);
    }

    public function updateQueque($id, $return): bool
    {
        $email = $this->emailQueue->findOneBy(['id' => $id]);

        if (true === $return) {
            $email->setStatus(true);
        } else {
            $email->setStatus(false);
        }

        $email->setPostDate(new \DateTime());

        $this->emailQueue->save($email, true);

        return true;
    }

    public function updateQuequeFalse($id): bool
    {
        $email = $this->emailQueue->findOneBy(['id' => $id]);
        $email->setStatus(false);

        $this->emailQueue->save($email, true);

        return true;
    }

    private function countDuration($time): bool|string|null
    {
        $dt = new \DateTime();
        $dtC = clone $dt;
        $dtC->modify("+{$time} seconds");
        $diff = $dt->diff($dtC);

        // przypisywanie czasów do tablicy array
        $time = [
            'years' => $diff->format('%y'),
            'months' => $diff->format('%m'),
            'days' => $diff->format('%d'),
            'hours' => $diff->format('%h'),
            'minutes' => $diff->format('%i'),
            'seconds' => $diff->format('%s'),
        ];

        $return = '';
        if (!$time['years'] && !$time['months'] && !$time['days'] && !$time['hours'] && !$time['minutes'] && !$time['seconds']) {
            $return = '------------------';
        }

        if ($time['years']) {
            $return .= $this->stringOfNum($time['years'], ['rok', 'lata', 'lat']).' ';
        }
        if ($time['months']) {
            $return .= $this->stringOfNum($time['months'], ['mc', 'mce', 'mcy']).' ';
        }
        if ($time['days']) {
            $return .= $this->stringOfNum($time['days'], ['dzień', 'dni', 'dni']).' ';
        }
        if ($time['hours']) {
            $return .= $this->stringOfNum($time['hours'], ['godzina', 'godziny', 'godzin']).' ';
        }
        if ($time['minutes']) {
            $return .= $this->stringOfNum($time['minutes'], ['minuta', 'minuty', 'minut']).' ';
        }
        if ($time['seconds']) {
            $return .= $this->stringOfNum($time['seconds'], ['sekunda', 'sekundy', 'sekund']);
        }

        return mb_ereg_replace('^\s*([\s\S]*?)\s*$', '\1', $return);
    }

    private function stringOfNum($number, $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $number.' '.$titles[$number % 100 > 4 && $number % 100 < 20 ? 2 : $cases[min($number % 10, 5)]];
    }
}
