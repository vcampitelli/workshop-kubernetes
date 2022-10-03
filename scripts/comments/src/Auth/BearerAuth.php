<?php

declare(strict_types=1);

namespace App\Auth;

class BearerAuth
{

    /**
     * Efetua o login a partir de uma autenticação HTTP Bearer
     *
     * @param string $auth
     *
     * @return string|null
     */
    public function __invoke(string $auth): ?string
    {
        [$type, $token] = explode(' ', $auth);
        if (($type !== 'Bearer') || (empty($token))) {
            return null;
        }

        return $token;
    }

}
