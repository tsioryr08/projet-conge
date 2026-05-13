<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check session and optional role argument.
     * Usage in routes: ['filter' => 'auth:employe'] or ['filter' => 'auth:rh,admin']
     */
   public function before(RequestInterface $request, $arguments = null)
{
    $session = session();

    if (! $session->get('isLoggedIn')) {
        return redirect()->to('/login'); // ✅ corrigé
    }

    if (! empty($arguments)) {
        $allowed = is_array($arguments) ? $arguments : explode(',', $arguments);
        $role = $session->get('role') ?? '';
        if (! in_array($role, $allowed)) {
            return redirect()->to('/login'); // ✅ corrigé
        }
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
