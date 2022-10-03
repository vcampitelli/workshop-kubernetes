<?php

use App\Auth\BasicAuth;
use App\Auth\PasswordLogin;
use App\User;

require __DIR__ . '/../vendor/autoload.php';

if (rtrim($_SERVER['REQUEST_URI'], '/') !== '/auth') {
    http_response_code(404);
    die(2);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(1);
}

$user = (function (array $server): ?User {
    if (empty($server['HTTP_AUTHORIZATION'])) {
        return null;
    }

    $basicAuth = new BasicAuth();
    $check = $basicAuth($server['HTTP_AUTHORIZATION']);
    if (empty($check)) {
        return null;
    }

    [$username, $password] = $check;
    $login = new PasswordLogin();
    $user = $login($username, $password);
    if (!$user) {
        return null;
    }

    return $user;
})($_SERVER);

if (!$user) {
    http_response_code(401);
    die(3);
}

$_ENV['JWT_ISSUER'] = 'Vinícius Campitelli @ PHPeste';
$_ENV['JWT_PRIVATE_KEY'] = 'LS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tCk1JR0hBZ0VBTUJNR0J5cUdTTTQ5QWdFR0NDcUdTTTQ5QXdFSEJHMHdhd0lCQVFRZ1VzY2d6Y3NQTEluMC9CT3oKbkE4RkFQaURkSjJDb0hTQmNVd2dNQjgvY1c2aFJBTkNBQVJtaENVa2dJdXZQejJMT2l1N1Y3WS9ESXJLM1NsdgpiU3JYOTRPVkFRd3pVVHdObUNidk1JVG43N0pFRVVaNXhvMFoxTnVzWUViTVpvbm12TnF3amdwegotLS0tLUVORCBQUklWQVRFIEtFWS0tLS0tCg==';
$_ENV['JWT_AUDIENCE'] = 'Kubernetes';

$issuer = new App\Auth\JwtIssuer(
    $_ENV['JWT_ISSUER'],
    $_ENV['JWT_PRIVATE_KEY'],
);
$token = $issuer($user, $_ENV['JWT_AUDIENCE']);

header('Content-type: application/json');
/** @var \DateTimeImmutable $expiration */
$expiration = $token->claims()->get('exp');
echo json_encode([
    'access_token' => $token->toString(),
    'expiration' => $expiration->format('c'),
]);
