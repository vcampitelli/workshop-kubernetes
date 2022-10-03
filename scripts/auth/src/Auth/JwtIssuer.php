<?php

declare(strict_types=1);

namespace App\Auth;

use App\User;
use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\UnencryptedToken;

class JwtIssuer
{

    /**
     * @var \Lcobucci\JWT\Signer\Key
     */
    private Key $signingKey;

    /**
     * @param string $issuer
     * @param string $privateKeyOrPath
     */
    public function __construct(private readonly string $issuer, string $privateKeyOrPath)
    {
        $this->signingKey = (\str_starts_with($privateKeyOrPath, 'file://'))
            ? InMemory::file(\substr($privateKeyOrPath, 7))
            : InMemory::plainText($privateKeyOrPath);
    }

    /**
     * @param \App\User $user
     * @param string    $audience
     *
     * @return \Lcobucci\JWT\UnencryptedToken
     */
    public function __invoke(User $user, string $audience): UnencryptedToken
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = Sha256::create();

        $now = new DateTimeImmutable();
        return $tokenBuilder
            ->issuedBy($this->issuer)
            ->permittedFor($audience)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo($user->username)
            ->withClaim('scopes', $user->scopes)
            ->getToken($algorithm, $this->signingKey);
    }

}
