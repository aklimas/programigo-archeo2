<?php
/*
 * Serwis służący do weryfikacji zmiennych
 *  - nie wymaga instalowania żadnych dodatkowych komponentów, wystarczy go wstawić do projektu
*/

namespace App\Service;

class ValidationService
{
    public function sanitizeString($value)
    {
        // usuwa ze stringa zabronione znaki
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    public function sanitizeInteger($value)
    {
        // usuwa ze stringa zabronione znaki
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
