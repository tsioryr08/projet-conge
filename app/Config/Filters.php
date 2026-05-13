<?php

namespace Config;

use App\Filters\Auth;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    // Déclaration des alias de filtres
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => Auth::class,   // ← notre filtre
    ];

    // Filtres appliqués à toutes les requêtes
    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['login']],
        ],
        'after' => [
            'toolbar',
        ],
    ];

    public array $methods = [];
    public array $filters = [];
}