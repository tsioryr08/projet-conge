<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    // ✅ utilise isLoggedIn et role directement
    if (! session()->get('isLoggedIn')) {
        return redirect()->to(base_url('login'));
    }

    if (! empty($arguments)) {
        $role = session()->get('role') ?? '';
        if (! in_array($role, $arguments, true)) {
            $path = match ($role) {
                'admin'   => 'admin',
                'rh'      => 'rh',
                'employe' => 'employe',
                default   => 'login',
            };
            return redirect()->to(base_url($path));
        }
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après la requête
    }
}