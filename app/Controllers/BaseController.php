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
        $session = service('session');
        $employeId = $session->get('employe_id');
        
        if (!$employeId) {
            return null;
        }
        
        return [
            'id' => (int) $employeId,
            'email' => $session->get('email'),
            'nom' => $session->get('nom'),
            'prenom' => $session->get('prenom'),
            'role' => $session->get('role'),
            'isLoggedIn' => $session->get('isLoggedIn'),
        ];
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