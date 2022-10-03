<?php

declare(strict_types=1);

namespace App\Auth;

use App\User;

class PasswordLogin
{

    /**
     * @var array|array[]
     */
    private static array $loginCredentials = [
        [
            'id'       => 1,
            'username' => 'admin',
            'password' => 'admin',
            'scopes'   => ['posts', 'comments'],
        ],
        [
            'id'       => 2,
            'username' => 'nocomments',
            'password' => 'nocomments',
            'scopes'   => ['posts'],
        ],
    ];

    /**
     * Efetua o login falso, retornando o usuário das credenciais passadas ou null se não houver
     *
     * @param string $username
     * @param string $password
     *
     * @return \App\User|null
     */
    public function __invoke(string $username, string $password): ?User
    {
        foreach (self::$loginCredentials as $credential) {
            if (($credential['username'] === $username) && ($credential['password'] === $password)) {
                return new User(
                    $credential['id'],
                    $credential['username'],
                    $credential['scopes'],
                );
            }
        }
        return null;
    }

}
