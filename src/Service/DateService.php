<?php

namespace App\Service;

class DateService
{
    public function addOneMonth($date): \DateTime
    {
        $addMonth = new \DateTime($date->format('Y-m-d'));
        $day = $addMonth->format('j');
        $addMonth->modify('first day of +1 month');
        $addMonth->modify('+'.(min($day, $addMonth->format('t')) - 1).' days');

        return $addMonth;
    }

    public function timeago($datetime, $full = false)
    {
        $now = new \DateTime();
        $ago = $datetime;
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'lat',
            'm' => 'miesięcy',
            'w' => 'tygodni',
            'd' => 'dni',
            'h' => 'godzin',
            'i' => 'minut',
            's' => 'sekund',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k.' '.$v.($diff->$k < 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string).' temu' : 'teraz';
    }

    public function randomTime($min = 0, $max = 23)
    {
        return mt_rand($min, $max).':'.str_pad(mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
    }

    public function addAnyNumberOfMonth($date, $howMany): \DateTime
    {
        if ($howMany > 0) {
            $addMonth = new \DateTime($date->format('Y-m-d'));
            $day = $addMonth->format('j');
            $addMonth->modify('first day of +'.$howMany.' month');
            $addMonth->modify('+'.(min($day, $addMonth->format('t')) - 1).' days');

            return $addMonth;
        } else {
            return $date;
        }
    }

    public function addAnyDaysToDate($date, $howMany): \DateTime
    {
        if ($howMany > 0) {
            $newDate = $date;
            $newDate->add(new \DateInterval('P'.$howMany.'D')); // P1D means a period of 1 day

            return $newDate;
        } else {
            return $date;
        }
    }



}
