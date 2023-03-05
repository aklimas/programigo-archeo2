<?php

namespace App\Service;

use App\Repository\UserRepository;

/**
 * Class TextService.
 */
class UserService
{
    public $user;

    public function __construct(UserRepository $userRepository)
    {
        $this->user = $userRepository;
    }

    public function userData($user)
    {
        $result = $this->user->findOneBy(['id' => $user]);
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }

    public function replaceData($user, $data, $footer = false)
    {
        $user = $this->userData($user);

        if (null !== $user) {
            $data = str_replace('[imie]', $user->getUserData()->getName(), $data);
        }

        if (false === $footer) {
            if (null !== $user) {
                $stopka = $user->getUserEmail()->getFooter();
                $stopka = preg_replace("/\s\s+/", '', $stopka);
                $data = str_replace('[stopka]', $stopka, $data);
            }
        } else {
            $footer = preg_replace("/\s\s+/", '', $footer);
            $data = str_replace('[stopka]', $footer, $data);
        }

        return $data;
    }
}
