<?php

declare(strict_types=1);

namespace App\Auth;

class BasicAuth
{

    /**
     * Efetua o login a partir de uma autenticação HTTP Basic
     *
     * @param string $auth
     *
     * @return array|null
     */
    public function __invoke(string $auth): ?array
    {
        [$type, $auth] = explode(' ', $auth);
        if ($type !== 'Basic') {
            return null;
        }

        $auth = base64_decode($auth);
        if (empty($auth)) {
            return null;
        }

        [$username, $password] = explode(':', $auth);
        if ((empty($username)) || (empty($password))) {
            return null;
        }

        return [$username, $password];
    }

}
