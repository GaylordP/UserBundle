<?php

namespace GaylordP\UserBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $security;
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public function onLogoutSuccess(Request $request): Response
    {
        if (null !== $this->security->getUser()) {
            $request->getSession()->getFlashBag()->add(
                'success',
                [
                    'user.logout_successfully',
                    [
                        '%username%' => $this->security->getUser()->getUsername(),
                    ],
                    'user'
                ]
            );
        } else {
            $request->getSession()->getFlashBag()->add(
                'danger',
                [
                    'login.required',
                    [],
                    'user'
                ]
            );
        }

        return new RedirectResponse($this->urlGenerator->generate('login'));
    }
}
