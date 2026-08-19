<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Helpers cargados automáticamente en todos los controladores hijos.
     * NOTA: No declarar tipo (array) porque la propiedad padre no lo tiene.
     */
    protected $helpers = ['form', 'url', 'permisos'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Carga explícita de helpers
        helper($this->helpers);
    }

    /**
     * Respuesta JSON estándar. Incluye siempre el token CSRF vigente
     * (csrf_header/csrf_hash) para que el frontend pueda refrescarlo
     * tras cada llamada AJAX sin necesidad de recargar la página
     * (necesario porque csrf.regenerate = true en este proyecto).
     */
    protected function jsonResponse(bool $success, string $message, array $data = [], int $code = 200): ResponseInterface
    {
        return $this->response->setJSON([
            'success'     => $success,
            'message'     => $message,
            'data'        => $data,
            'csrf_header' => csrf_header(),
            'csrf_hash'   => csrf_hash(),
        ])->setStatusCode($code);
    }
}