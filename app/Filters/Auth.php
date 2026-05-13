<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');

        // Pas connecté → retour au login
        if (! is_array($user) || empty($user['role'])) {
            return redirect()->to(base_url('login'));
        }

        // Vérification du rôle si précisé dans le filtre (ex: auth:rh)
        if (! empty($arguments) && ! in_array($user['role'], $arguments, true)) {
            // Connecté mais mauvais rôle → rediriger vers son propre dashboard
            $path = match ($user['role']) {
                'admin'   => 'admin',
                'rh'      => 'rh',
                'employe' => 'employe',
                default   => 'login',
            };
            return redirect()->to(base_url($path));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après la requête
    }
}