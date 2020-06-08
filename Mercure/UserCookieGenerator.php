<?php

namespace GaylordP\UserBundle\Mercure;

use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

class UserCookieGenerator
{
    private $mercure_jwt_key;
    private $env;

    public function __construct(string $mercure_jwt_key, string $env)
    {
        $this->mercure_jwt_key = $mercure_jwt_key;
        $this->env = $env;
    }

    public function generate(User $user): Cookie
    {
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => [
                'https://bubble.lgbt/user/' . $user->getSlug(),
            ]])
            ->getToken(new Sha256(), new Key($this->mercure_jwt_key))
        ;

        if ('prod' === $this->env) {
            return new Cookie(
                'mercureAuthorization',
                $token,
                0,
                '/.well-known/mercure',
                null,
                true,
                true,
                false,
                Cookie::SAMESITE_STRICT,
            );
        } else {
            return new Cookie(
                'mercureAuthorization',
                $token,
                0,
                '/.well-known/mercure',
                null,
                false,
                true,
                false,
                Cookie::SAMESITE_LAX,
            );
        }
    }
}
