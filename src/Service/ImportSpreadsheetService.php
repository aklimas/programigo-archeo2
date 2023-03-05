<?php

/*
 * Importowanie plików CSV oraz EXEL
 * V.1.0.1

$readSpreadsheet = new ImportSpreadsheetService("file2.csv");
$readSpreadsheet->getHeader();
$readSpreadsheet->getData();

*/

namespace App\Service;

class ImportSpreadsheetService
{
    private $file;
    private $reader;

    public function __construct($file)
    {
        $this->file = $file;
        $this->reader = null;
    }

    public function checkFile(): bool
    {
        $file_parts = pathinfo($this->file);
        switch ($file_parts['extension']) {
            case 'csv' : $this->reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                break;
            case 'xlsx' : $this->reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                break;
            default: return false;
        }

        return true;
    }

    public function readFile()
    {
        if (true === $this->checkFile()) {
            $spreadsheet = $this->reader->load($this->file);

            return $spreadsheet->getActiveSheet()->toArray();
        } else {
            return false;
        }
    }

    public function getHeaders()
    {
        $sheetData = $this->readFile();

        return $sheetData[0];
    }

    public function getData(): array
    {
        $text = new TextService();

        $sheetData = $this->readFile();
        $array = [];

        $i = 0;
        $headers = $sheetData[0];
        unset($sheetData[0]);
        foreach ($sheetData as $k => $t) {
            foreach ($t as $k2 => $t2) {
                $array[$k][$text->replaceTitles($headers[$k2])] = $t2;
            }
            ++$i;
        }

        return $array;
    }
}
