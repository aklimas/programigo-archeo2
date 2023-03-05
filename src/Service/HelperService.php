<?php

namespace App\Service;

use App\Entity\Core\TicketNo;
use App\Repository\Core\TicketNoRepository;
use Symfony\Component\Security\Core\Security;

class HelperService
{
    public function __construct(
        private Security $security,
        private TicketNoRepository $ticketNo
    ) {
    }

    public function encode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function decode($data): bool|string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function ticketNo($workflow = 'none'): ?int
    {
        $result = $this->ticketNo->getByDate($workflow);
        if (!$result) {
            $result = new TicketNo();
            $result->setWorkflow($workflow);
            $result->setDate(new \DateTime());
            $result->setCount(1);
        } else {
            $i = $result->getCount();
            ++$i;
            $result->setCount($i);
        }
        $getCount = $result->getCount();

        $this->ticketNo->save($result, true);

        return $getCount;
    }

    public function randomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function status($item, $color = false, $action = false, $availability = false, $class = '')
    {
        $_status = null;
        $colorText = '#fff';
        if (method_exists($item, 'getStatus')) {
            $status = $item->getStatus();
        } else {
            $status = $item;
        }

        if ($status) {
            if (null !== $status->getVariants()) {
                $parentStatus = clone $status;

                if ($status->getVariants()->count() > 0) {
                    foreach ($status->getVariants() as $variant) {
                        if (in_array($variant->getRole(), $this->security->getUser()->getRoles(), true)) {
                            $_status = $variant;
                        } else {
                            $_status = $status;
                        }
                    }
                } else {
                    $_status = $parentStatus;
                }
                if (null !== $parentStatus->getColorText()) {
                    $colorText = $parentStatus->getColorText();
                }

                if (false === $availability) {
                    if (true == $color) {
                        if (false !== $action) {
                            return '<a href="" class="badge '.$class.'" style="background-color: '.$parentStatus->getColor().'; color: '.$colorText.' ">'.$_status->getNameAction().'</a>';
                        } else {
                            return '<div class="badge '.$class.'" style="background-color: '.$parentStatus->getColor().'; color: '.$colorText.' ">'.$_status->getName().'</div>';
                        }
                    } else {
                        return $_status->getName();
                    }
                } else {
                    if (true === $_status->isAvailability()) {
                        if (true == $color) {
                            if (true === $action) {
                                return '<a href="" class="badge '.$class.'" style="background-color: '.$_status->getColor().'; color: '.$colorText.' ">'.$_status->getNameAction().'</a>';
                            } else {
                                return '<div class="badge '.$class.'" style="background-color: '.$_status->getColor().'; color: '.$colorText.' ">'.$_status->getName().'</div>';
                            }
                        } else {
                            return $_status->getName();
                        }
                    }
                }
            }
        }
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

    /**
     * shortens the supplied text after last word.
     *
     * @param string $string
     * @param int    $max_length
     * @param string $end_substitute  text to append, for example "..."
     * @param bool   $html_linebreaks if LF entities should be converted to <br />
     *
     * @return string
     */
    public function wordWrap($string, $max_length, $end_substitute = null, $html_linebreaks = true)
    {
        if ($html_linebreaks) {
            $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
        }
        $string = strip_tags($string); // gets rid of the HTML

        if (empty($string) || mb_strlen($string) <= $max_length) {
            if ($html_linebreaks) {
                $string = nl2br($string);
            }

            return $string;
        }

        if ($end_substitute) {
            $max_length -= mb_strlen($end_substitute, 'UTF-8');
        }

        $stack_count = 0;
        while ($max_length > 0) {
            $char = mb_substr($string, --$max_length, 1, 'UTF-8');
            if (preg_match('#[^\p{L}\p{N}]#iu', $char)) {
                ++$stack_count;
            } // only alnum characters
            elseif ($stack_count > 0) {
                ++$max_length;
                break;
            }
        }
        $string = mb_substr($string, 0, $max_length, 'UTF-8').$end_substitute;
        if ($html_linebreaks) {
            $string = nl2br($string);
        }

        return $string;
    }

    public function dateDiffInDays($date1, $date2): string
    {
        $date1 = new \DateTime($date1->format('Y-m-d'));
        $date2 = new \DateTime($date2->format('Y-m-d'));

        return $date1->diff($date2)->format('%r%a');
    }



}
