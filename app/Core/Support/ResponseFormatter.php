<?php

namespace App\Core\Support;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class ResponseFormatter
 *
 * Standarisasi struktur respons JSON untuk seluruh API dan Controller di MasjidCMS.
 */
class ResponseFormatter
{
    /**
     * Membentuk struktur respons sukses standar.
     *
     * @param ResponseInterface $response
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return ResponseInterface
     */
    public static function success(
        ResponseInterface $response,
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): ResponseInterface {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];

        return $response
            ->setStatusCode($code)
            ->setJSON($payload);
    }

    /**
     * Membentuk struktur respons error standar.
     *
     * @param ResponseInterface $response
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return ResponseInterface
     */
    public static function error(
        ResponseInterface $response,
        string $message = 'Error',
        mixed $errors = null,
        int $code = 400
    ): ResponseInterface {
        $payload = [
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ];

        return $response
            ->setStatusCode($code)
            ->setJSON($payload);
    }
}
