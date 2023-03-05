<?php

namespace App\Twig;

use App\Repository\Core\SettingsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class FileExtension.
 */
class SettingsExtension extends AbstractExtension
{
    private SettingsRepository $setting;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->setting = $settingsRepository;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSetting', [$this, 'getSetting']),
        ];
    }

    public function getSetting($value, $file = false): \App\Entity\Core\Files\Files|string|null
    {
        return $this->setting->get($value, $file);
    }
}
