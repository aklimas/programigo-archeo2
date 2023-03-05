<?php

namespace App\Twig;

use App\Service\UserService;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class FileExtension.
 */
class UserExtension extends AbstractExtension
{
    public function __construct(
        private UserService $user,
        private Security $security,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getProfilePhoto', [$this, 'getProfilePhoto']),
        ];
    }

    public function getProfilePhoto($class = 'rounded-circle img-profile border', $user = null)
    {
        $package = new Package(new EmptyVersionStrategy());

        if (null === $user) {
            $_user = $this->user->userData($this->security->getUser());
        } else {
            $_user = $this->user->user->findOneBy(['id' => $user]);
        }

        if ($_user->getUserData()->getPhoto()) {
            $src = $_user->getUserData()->getPhoto()->getPath().$_user->getUserData()->getPhoto()->getName();

            return '<img src="'.$src.'" alt="'.$_user->getUserData()->getPhoto()->getAlt().'" class="'.$class.'">';
        } else {
            return '<img src="'.$package->getUrl('/panel/assets/img/emptyProfile.png').'" alt="Profile" class="'.$class.'">';
        }
    }
}
