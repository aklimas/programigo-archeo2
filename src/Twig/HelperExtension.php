<?php

namespace App\Twig;

use App\Service\HelperService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class FileExtension.
 */
class HelperExtension extends AbstractExtension
{
    public function __construct(
        private HelperService $helper
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dateDiffInDays', [$this, 'dateDiffInDays']),
            new TwigFunction('status', [$this, 'status']),
            new TwigFunction('getEmailGate', [$this, 'getEmailGate']),
            new TwigFunction('integer', [$this, 'integer']),
            new TwigFunction('timeago', [$this, 'timeago']),
            new TwigFunction('wordWrap', [$this, 'wordWrap']),
            new TwigFunction('jsonDecode', [$this, 'jsonDecode']),
        ];
    }

    public function dateDiffInDays($date1, $date2)
    {
        $beetwen = $this->helper->dateDiffInDays($date1, $date2);

        if (0 == $beetwen) {
            return '<strong class="text-danger">'.(int) $beetwen.'</strong>';
        } elseif ($beetwen < 0) {
            return '<strong class="text-danger">'.$beetwen.'</strong>';
        } elseif ($beetwen > 0 and $beetwen <= 14) {
            return '<strong class="text-warning">'.$beetwen.'</strong>';
        } else {
            return '<strong class="text-success">'.$beetwen.'</strong>';
        }
    }

    public function status($item, $class = '')
    {
        return $this->helper->status($item, true, false, false, $class);
    }

    public function integer($value)
    {
        return (int) $value;
    }

    public function timeago($date)
    {
        return $this->helper->timeago($date);
    }

    public function wordWrap($string, $max_length, $end_substitute = null, $html_linebreaks = true)
    {
        return $this->helper->wordWrap($string, $max_length, $end_substitute = null, $html_linebreaks = true);
    }

    public function jsonDecode($str)
    {
        return json_decode($str);
    }
}
