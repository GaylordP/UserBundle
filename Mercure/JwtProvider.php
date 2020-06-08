<?php

namespace GaylordP\UserBundle\Mercure;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JwtProvider
{
    private $mercure_jwt_key;

    public function __construct(string $mercure_jwt_key)
    {
        $this->mercure_jwt_key = $mercure_jwt_key;
    }

    public function __invoke(): string
    {
        return (new Builder())
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken(new Sha256(), new Key($this->mercure_jwt_key))
        ;
    }
}
