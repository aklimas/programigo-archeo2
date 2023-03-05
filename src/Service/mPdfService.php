<?php

namespace App\Service;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

class mPdfService
{
    // do działania wymagana jest instalacja mPDF za pomocą komendy:
    // composer require mpdf/mpdf

    /**
     * @throws MpdfException
     */
    public function generateSimplePDF($args = null)
    {
        if (!isset($args['filename'])) {
            $filename = null;
        } else {
            $filename = $args['filename'];
        }
        if (!isset($args['download'])) {
            $download = null;
        } else {
            $download = $args['download'];
        }
        if (!isset($args['css'])) {
            $css = null;
        } else {
            $css = $args['css'];
        }
        if (!isset($args['content'])) {
            $content = null;
        } else {
            $content = $args['content'];
        }
        if (!isset($args['header'])) {
            $header = null;
        } else {
            $header = $args['header'];
        }
        if (!isset($args['footer'])) {
            $footer = null;
        } else {
            $footer = $args['footer'];
        }
        if (!isset($args['watermark'])) {
            $watermark = null;
        } else {
            $watermark = $args['watermark'];
        }
        if (!isset($args['save'])) {
            $save = null;
        } else {
            $save = $args['save'];
        }
        if (!isset($args['size'])) {
            $size = 'A4';
        } else {
            $size = $args['size'];
        }

        $filename = preg_replace("/\r|\n/", '', $filename);
        $filename = preg_replace('/\s+/', '', $filename);

        // funkcja służy do generowania prostych dokumentów pdf w ramach których każda strona posiada ten sam nagłówek, stopkę oraz style
        // z wykorzystaniem tylko jednego arkusza stylów

        /* argumenty przekazywane w wywołaniu funkcji:
         * $filename = nazwa pliku bez rozszerzenia (w przypadku braku, plik otrzyma nazwę pdf.pdf)
         * $download = true -> jeśli plik ma zostać od razu pobrany na dysk, w przeciwnym razie zostanie tylko wyświetlony
         * $css = ścieżka do pliku ze stylami
         * $content = zawartość strony, która będzie generowana do wersji pdf
         * $header = nagłówek strony w formacie html
         * $footer = stopka w formacie html
         * $watermark = ścieżka do pliku ze znakiem wodnym, w przypadku braku znak wodny nie jest nakładany
         * $save = czy ma zapisać plik na serwerze, jeśli F to zapisuje na serwerze
        */

        /* możliwe do zastosowanie zmienne wewnętrzne wykorzystywane przed mPdf:
         * {PAGENO} / {nb} - format numerowania, w tym przypadku "nr strony/iczba stron" (do dodania np. w stopce)
         */

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => $size]);
        $mpdf->curlAllowUnsafeSslRequests = true;
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->allow_charset_conversion = true;
        $mpdf->charset_in = 'UTF-8';
        if (null != $css) {
            //  $mpdf->WriteHTML(file_get_contents($css), 1);
        }
        $mpdf->list_indent_first_level = 0;
        if (null != $header) {
            $mpdf->SetHTMLHeader($header, 'O', true);
        }
        $mpdf->AddPage('', '', '', '', '', 20, 20, 30, 30, 15, 20); // left, right, top, bottom
        $mpdf->SetDisplayMode('fullpage');
        if (null != $footer) {
            $mpdf->setFooter($footer);
        }
        if (null != $watermark) {
            $mpdf->SetWatermarkImage($watermark);
            $mpdf->showWatermarkImage = true;
        }
        // $mpdf->showImageErrors = true;
        if (null != $content) {
            $mpdf->WriteHTML($content);
        }
        $mpdf->setHeader();
        if (null == $filename) {
            $filename = 'pdf';
        }
        if (true == $save) {
            $output = 'F';
            $filename = 'AdvertisementPosters/'.$filename;
        } elseif (true == $download) {
            $output = 'D';
        } else {
            $output = 'I';
        }

        $filename = preg_replace("/\r|\n/", '', $filename);
        $mpdf->Output($filename.'.pdf', $output);
    }
}
