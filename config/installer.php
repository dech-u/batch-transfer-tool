<?php

use Illuminate\Support\Facades\File;

return [
    'icon' => 'assets/horizontal-logo.svg',

    //    'background' => 'assets/logo.svg',

    'support_url' => 'https://join.skype.com/invite/xWFoykc1gnN6',

    'server' => [
        'php'       => [
            'name'    => 'PHP Version',
            'version' => '>= 8.1.0',
            'check'   => fn() => version_compare(PHP_VERSION, '8', '>')
        ],
        'pdo'       => [
            'name'  => 'PDO',
            'check' => fn() => extension_loaded('pdo_mysql')
        ],
        'mbstring'  => [
            'name'  => 'Mbstring extension',
            'check' => fn() => extension_loaded('mbstring')
        ],
        'fileinfo'  => [
            'name'  => 'Fileinfo extension',
            'check' => fn() => extension_loaded('fileinfo')
        ],
        'openssl'   => [
            'name'  => 'OpenSSL extension',
            'check' => fn() => extension_loaded('openssl')
        ],
        'tokenizer' => [
            'name'  => 'Tokenizer extension',
            'check' => fn() => extension_loaded('tokenizer')
        ],
        'json'      => [
            'name'  => 'Json extension',
            'check' => fn() => extension_loaded('json')
        ],
        'curl'      => [
            'name'  => 'Curl extension',
            'check' => fn() => extension_loaded('curl')
        ],
        'zip'       => [
            'name'  => 'Zip extension',
            'check' => fn() => extension_loaded('zip')
        ]
    ],

    'folders' => [
        'storage.framework' => [
            'name'  => base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework',
            'check' => fn() => (int)File::chmod(base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework') >= 755
        ],
        'storage.logs'      => [
            'name'  => base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs',
            'check' => fn() => (int)File::chmod(base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs') >= 755
        ],
        'storage.cache'     => [
            'name'  => base_path() . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache',
            'check' => fn() => (int)File::chmod(base_path() . DIRECTORY_SEPARATOR . 'bootstrap/cache') >= 755
        ],
    ],

    'database' => [
        'seeders' => false
    ],

    'commands' => [
        'db:seed --class=InstallationSeeder',
        'db:seed --class=AddSuperAdminSeeder',
    ],

    'admin_area' => [
        'user' => [
            'email'    => 'superadmin@gmail.com',
            'password' => 'superadmin'
        ]
    ]
];
