<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Http;

trait EventHelpers
{
    /**
     * Format nomor HP Indonesia: ubah prefix 0 jadi 62.
     */
    protected function formatPhone(string $phone): string
    {
        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Kirim pesan WhatsApp via Fonnte API.
     */
    protected function sendWhatsApp(string $phone, string $message): void
    {
        Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target'   => $phone,
            'message'  => $message,
        ]);
    }

    /**
     * Generate QR code PNG (raw bytes) dari teks menggunakan GD + BaconQrCode.
     */
    protected function generateQrPngGD(string $text, int $size = 300): string
    {
        $matrix = \BaconQrCode\Encoder\Encoder::encode(
            $text,
            \BaconQrCode\Common\ErrorCorrectionLevel::L(),
            ''
        )->getMatrix();

        $matrixWidth  = $matrix->getWidth();
        $matrixHeight = $matrix->getHeight();
        $pixelSize    = max(1, (int) floor($size / $matrixWidth));
        $realSize     = $pixelSize * $matrixWidth;

        $img   = imagecreatetruecolor($realSize, $realSize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, $realSize, $realSize, $white);

        for ($row = 0; $row < $matrixHeight; $row++) {
            for ($col = 0; $col < $matrixWidth; $col++) {
                if ($matrix->get($col, $row)) {
                    imagefilledrectangle(
                        $img,
                        $col * $pixelSize,
                        $row * $pixelSize,
                        ($col + 1) * $pixelSize - 1,
                        ($row + 1) * $pixelSize - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png;
    }
}