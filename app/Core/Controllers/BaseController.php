<?php

namespace App\Core\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Core\Support\ResponseFormatter;

/**
 * Class BaseController
 *
 * Parent class untuk seluruh Controller dalam MasjidCMS.
 * Menyediakan pengolahan request, response, logging, dan helper global.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance request yang sedang berjalan.
     *
     * @var IncomingRequest|CLIRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * Instance logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Inisialisasi Controller, memuat helper dan properti bawaan.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->logger = $logger;
    }

    /**
     * Mengembalikan response sukses standar.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return ResponseInterface
     */
    protected function respondSuccess(mixed $data = null, string $message = 'Success', int $code = 200): ResponseInterface
    {
        return ResponseFormatter::success($this->response, $data, $message, $code);
    }

    /**
     * Mengembalikan response error standar.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return ResponseInterface
     */
    protected function respondError(string $message = 'Error', mixed $errors = null, int $code = 400): ResponseInterface
    {
        return ResponseFormatter::error($this->response, $message, $errors, $code);
    }

    protected function destructivePostButton(string $url, string $label, string $confirmMessage, string $style = ''): string
    {
        $confirm = esc($confirmMessage, 'js');
        $buttonStyle = $style !== '' ? $style : 'padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);';

        return '<form action="' . esc($url) . '" method="POST" onsubmit="return confirm(\'' . $confirm . '\')" style="display:inline;">'
            . csrf_field()
            . '<button type="submit" class="btn btn-secondary" style="' . esc($buttonStyle) . '">' . esc($label) . '</button>'
            . '</form>';
    }
}
