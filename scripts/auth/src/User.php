<?php

declare(strict_types=1);

namespace App;

class User
{

    /**
     * @param int $id
     * @param string $username
     * @param array  $scopes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly array $scopes,
    ) {
    }

}
