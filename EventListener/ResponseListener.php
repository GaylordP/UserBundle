<?php

namespace GaylordP\UserBundle\EventListener;

use GaylordP\UserBundle\Mercure\UserCookieGenerator;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class ResponseListener
{
    private $security;
    private $userCookieGenerator;

    public function __construct(
        Security $security,
        UserCookieGenerator $userCookieGenerator
    ) {
        $this->security = $security;
        $this->userCookieGenerator = $userCookieGenerator;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        if (null !== $this->security->getUser()) {
            $event
                ->getResponse()
                ->headers
                ->setCookie($this->userCookieGenerator->generate($this->security->getUser()))
            ;
        }
    }
}
