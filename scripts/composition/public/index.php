<?php

use App\Auth\BearerAuth;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

if (empty($_ENV['APP_URL_POSTS'])) {
    throw new \RuntimeException('Variável APP_URL_POSTS não configurada');
}
if (empty($_ENV['APP_URL_COMMENTS'])) {
    throw new \RuntimeException('Variável APP_URL_COMMENTS não configurada');
}

require __DIR__ . '/../vendor/autoload.php';

if (rtrim($_SERVER['REQUEST_URI'], '/') !== '') {
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
})(
    $_SERVER
);

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

$urls = [$_ENV['APP_URL_POSTS']];
if (\in_array('comments', $scopes)) {
    $urls[] = $_ENV['APP_URL_COMMENTS'];
}

$curlMaster = \curl_multi_init();
$handlers = [];
$headers = [
    "Authorization: {$_SERVER['HTTP_AUTHORIZATION']}",
];
if (!empty($_SERVER['X_SLEEP_FAIL'])) {
    $headers[] = 'X-Sleep-Fail: 1';
}
foreach ($urls as $url) {
    $handler = \curl_init();
    \curl_setopt_array($handler, [
        CURLOPT_URL            => $url,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $handlers[] = $handler;
    \curl_multi_add_handle($curlMaster, $handler);
}

do {
    $status = \curl_multi_exec($curlMaster, $active);
    if ($active) {
        \curl_multi_select($curlMaster);
    }
} while ($active && $status == CURLM_OK);

$posts = \curl_multi_getcontent($handlers[0]);

// Processando as postagens para adicionar os comentários
if (!empty($handlers[1])) {
    $posts = \json_decode($posts, true);
    if (empty($posts)) {
        \http_response_code(500);
        die();
    }

    $comments = \curl_multi_getcontent($handlers[1]);
    $comments = \json_decode($comments, true);
    if (!empty($comments)) {
        foreach ($posts as $i => $post) {
            $posts[$i]['comments'] = \array_values(
                (array) \array_filter($comments, fn($comment) => $comment['postId'] === $post['id'])
            );
        }
    }

    $posts = \json_encode($posts);
}

foreach ($handlers as $handler) {
    \curl_multi_remove_handle($curlMaster, $handler);
}

\header('Content-type: application/json');
echo $posts;
