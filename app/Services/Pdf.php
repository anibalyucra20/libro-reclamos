<?php

declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;

final class Pdf
{
    public static function fromHtml(string $html, string $paper = 'A4', string $orientation = 'portrait'): string
    {
        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $dompdf->setPaper($paper, $orientation);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        return $dompdf->output();
    }
}
