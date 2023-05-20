<?php

use App\Auth\BearerAuth;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

require __DIR__ . '/../vendor/autoload.php';

if ((!empty($_SERVER['HTTP_X_FAIL'])) && (random_int(0, 1) === 1)) {
    http_response_code(503);
    die();
}

if (!empty($_SERVER['HTTP_X_SLEEP_FAIL'])) {
    sleep(5);
    http_response_code(504);
    die();
}

if (rtrim($_SERVER['REQUEST_URI'], '/') !== '/posts') {
    http_response_code(404);
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    die();
}

$token = (function (array $server): ?string {
    if (empty($server['HTTP_AUTHORIZATION'])) {
        return null;
    }

    $bearerAuth = new BearerAuth();
    return $bearerAuth($server['HTTP_AUTHORIZATION']);
})($_SERVER);

if (empty($token)) {
    http_response_code(401);
    die();
}

try {
    $verifier = new App\Auth\JwtVerifier(
        $_ENV['JWT_ISSUER'],
        $_ENV['JWT_PUBLIC_KEY'],
    );
    $token = $verifier($token, $_ENV['JWT_AUDIENCE']);
} catch (RequiredConstraintsViolated $e) {
    http_response_code(401);
    header('Content-type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
    ]);
    die();
} catch (\Throwable $t) {
    http_response_code(401);
    die();
}

$scopes = $token->claims()->get('scopes');
if ((empty($scopes)) || (!\is_array($scopes)) || (!\in_array('posts', $scopes))) {
    http_response_code(403);
    die();
}

\header('X-App-Version: 0.0.1');
\header('Content-type: application/json');
$ch = \curl_init();
\curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://jsonplaceholder.typicode.com/posts',
    CURLOPT_CONNECTTIMEOUT => 3,
]);
\curl_exec($ch);
\curl_close($ch);
