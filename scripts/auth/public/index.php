<?php

use App\Auth\BasicAuth;
use App\Auth\PasswordLogin;
use App\User;
use Lcobucci\JWT\Token\RegisteredClaims;

require __DIR__ . '/../vendor/autoload.php';

if (rtrim($_SERVER['REQUEST_URI'], '/') !== '/auth') {
    http_response_code(404);
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die();
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
    die();
}

$issuer = new App\Auth\JwtIssuer(
    $_ENV['JWT_ISSUER'],
    $_ENV['JWT_PRIVATE_KEY'],
);
$token = $issuer($user, $_ENV['JWT_AUDIENCE']);

\header('Content-type: application/json');
/** @var \DateTimeImmutable $expiration */
$expiration = $token->claims()->get(RegisteredClaims::EXPIRATION_TIME);
echo json_encode([
    'access_token' => $token->toString(),
    'expiration'   => $expiration->format('c'),
]);
