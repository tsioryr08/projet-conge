<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text'];

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
    }

    protected function currentUser(): ?array
    {
        $user = service('session')->get('user');  // ← service() au lieu de $this->session
        return is_array($user) ? $user : null;
    }

    protected function dashboardPathForRole(?string $role): string
    {
        return match ($role) {
            'admin'   => 'admin',
            'rh'      => 'rh',
            'employe' => 'employe',
            default   => 'login',
        };
    }
}