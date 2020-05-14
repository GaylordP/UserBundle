<?php

namespace GaylordP\UserBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Twig\Environment;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $security;
    private $urlGenerator;
    private $twig;

    public function __construct(
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function onLogoutSuccess(Request $request): Response
    {
        if (null !== $this->security->getUser()) {
            $request->getSession()->getFlashBag()->add(
                'success',
                [
                    'user.logout_successfully',
                    [
                        '%username%' => $this->twig->render('@User/button/_user.html.twig', [
                            'user' => $this->security->getUser(),
                        ]),
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
