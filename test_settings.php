<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/settings/desktop-integration',
        'POST',
        [
            'desktop_web_port' => 8800,
            'desktop_db_port' => 3308,
            'desktop_tunnel_enabled' => 1,
            'desktop_tunnel_token' => 'my-super-secret-cloudflare-tunnel-token'
        ]
    )
);
$kernel->terminate($request, $response);
echo "Written to .env.desktop:\n";
echo file_get_contents(__DIR__.'/.env.desktop');
