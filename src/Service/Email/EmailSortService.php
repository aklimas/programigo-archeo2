<?php

namespace App\Service\Email;

class EmailSortService
{
    protected array $emails;
    private array $queue;

    protected array $emailsServicesArray;
    protected array $emailsArray;

    protected int $countEmail;

    public function __construct()
    {
        $this->queue = [];
        $this->emailsServicesArray = [];
        $this->emailsArray = [];
    }

    public function get($array): array
    {
        $this->emails = $array;
        $this->countEmail = count($this->emails);
        $this->queue = array_fill(0, $this->countEmail, false);

        $this->uniqueHostEmail();
        $this->sortEmailByQuantity();
        $this->createNewArray();
        $this->filled();

        return $this->queue;
    }

    /**
     * etap I - odseparowujemy końcówkę maila i tworzymy tablicę systemów mailowych wraz z unikalnym id i liczbą maili w danym systemie
     * tworzymy też tablicę maili wraz z id systemu.
     */
    private function uniqueHostEmail()
    {
        foreach ($this->emails as $item) {
            $address = explode('@', $item);
            if (!in_array($address[1], array_column($this->emailsServicesArray, 'system'))) {
                $counter = count($this->emailsServicesArray) + 1;
                $this->emailsServicesArray[] = ['id' => $counter, 'system' => $address[1], 'count' => 1];
                $this->emailsArray[] = ['email' => $item, 'systemId' => $counter];
            } else {
                $counter = array_search($address[1], array_column($this->emailsServicesArray, 'system'));

                $this->emailsArray[] = ['email' => $item, 'systemId' => $this->emailsServicesArray[$counter]['id']];
                $this->emailsServicesArray[$counter]['count'] = $this->emailsServicesArray[$counter]['count'] + 1;
            }
        }
    }

    private function sortEmailByQuantity()
    {
        usort($this->emailsServicesArray, function ($a, $b) {
            return $a['count'] <= $b['count'];
        });
    }

    /**
     * etap III - tworzymy tablicę z kolejką maili z odpowiednim uszeregowaniem maili.
     */
    private function createNewArray()
    {
        foreach ($this->emailsServicesArray as $item) {
            // wykonuję tylko dla systemów dla których mam więcej niż jeden adres w bazie
            if ($item['count'] > 1) {
                $separator = floor($this->countEmail / $item['count']) + 1;
                // wyszukuje pierwszą wolną pozycję
                $i = 0;
                $added = 0;
                while ($added < $item['count'] && $i < $this->countEmail) {
                    $i = $this->findFreePlace($this->queue, $i);
                    // wyszukuję w tablicy dowolny adres email należący do tego systemu
                    $filterBy = $item['id'];
                    $currentEmail = current(array_filter($this->emailsArray, function ($var) use ($filterBy) {
                        return $var['systemId'] == $filterBy;
                    }));
                    // przypisuję adres email do kolejki
                    $this->queue[$i] = $currentEmail['email'];
                    // usuwam tego maila z tablicy maili
                    $this->emailsArray = $this->removeElementWithValue($this->emailsArray, 'email', $currentEmail['email']);
                    ++$added;
                    $i = $i + $separator;
                }
                $this->emailsServicesArray = $this->changeCount($this->emailsServicesArray, 'system', $item['system'], $added);
            }
        }
    }

// etap IV - wypełniamy tablicę kolejki po kolei tym, co zostało niezagospodarowane
    private function filled(): bool
    {
        $i = 0;
        foreach ($this->emailsArray as $item) {
            $i = $this->findFreePlace($this->queue, $i);
            $this->queue[$i] = $item['email'];
            $this->emailsArray = $this->removeElementWithValue($this->emailsArray, 'email', $item['email']);
        }

        return true;
    }

    private function changeCount($array, $key, $value, $added)
    {
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                $array[$subKey]['count'] = $array[$subKey]['count'] - $added;

                return $array;
            }
        }

        return false;
    }

    private function removeElementWithValue($array, $key, $value)
    {
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                unset($array[$subKey]);

                return $array;
            }
        }

        return false;
    }

    private function findFreePlace($queue, $i)
    {
        while (isset($queue[$i]) && false != $queue[$i]) {
            ++$i;
        }

        return $i;
    }
}
