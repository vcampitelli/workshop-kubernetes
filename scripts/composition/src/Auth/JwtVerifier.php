<?php

declare(strict_types=1);

namespace App\Auth;

use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;

class JwtVerifier
{

    /**
     * @var \Lcobucci\JWT\Signer\Key
     */
    private Key $key;

    /**
     * @param string $issuer
     * @param string $publicKeyOrPath
     */
    public function __construct(private readonly string $issuer, string $publicKeyOrPath)
    {
        $this->key = (\str_starts_with($publicKeyOrPath, 'file://'))
            ? InMemory::file(\substr($publicKeyOrPath, 7))
            : InMemory::plainText($publicKeyOrPath);
    }

    /**
     * @param string $jwt
     * @param string $audience
     *
     * @return \Lcobucci\JWT\UnencryptedToken
     */
    public function __invoke(string $jwt, string $audience): UnencryptedToken
    {
        return (new JwtFacade())->parse(
            $jwt,
            new Constraint\SignedWith(Sha256::create(), $this->key),
            new Constraint\StrictValidAt(
                new FrozenClock(new DateTimeImmutable())
            ),
            new Constraint\IssuedBy($this->issuer),
            new Constraint\PermittedFor($audience),
        );
    }

}
