<?php

namespace GaylordP\UserBundle\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Authentificator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $formLoginDefaultTargetPath;
    private $twig;
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        string $formLoginDefaultTargetPath,
        Environment $twig,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->formLoginDefaultTargetPath = $formLoginDefaultTargetPath;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    public function supports(Request $request)
    {
        return
            'login' === $request->attributes->get('_route')
                &&
            $request->isMethod('POST')
        ;
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('login')['email'],
            'password' => $request->request->get('login')['password'],
            'csrf_token' => $request->request->get('login')['_token'],
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['email']);

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        if ('' === $credentials['email']) {
            throw new CustomUserMessageAuthenticationException('user.email_required');
        }

        $this->entityManager->getFilters()->disable('deleted_at');
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($credentials['email']);
        $this->entityManager->getFilters()->enable('deleted_at');

        if (null === $user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException(
                'user.email.unexist',
                [
                    '%email%' => $credentials['email'],
                ]
            );
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (false === $this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException('user.password.wrong');
        }

        if (null !== $user->getDeletedAt()) {
            throw new CustomUserMessageAuthenticationException(
                'user.deleted',
                [
                    '%url%' => $this->urlGenerator->generate('contact'),
                ]
            );
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        $request->getSession()->getFlashBag()->add(
            'success',
            [
                'user.login_successfully',
                [
                    '%username%' => $this->twig->render('@User/button/_user.html.twig', [
                        'user' => $token->getUser(),
                    ]),
                ],
                'user'
            ]
        );

        return new RedirectResponse($this->urlGenerator->generate($this->formLoginDefaultTargetPath));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'status' => 'login_required',
                'title' => $this->translator->trans('Error', [], 'validators'),
                'body' => $this->translator->trans('login.required', [], 'user'),
            ], Response::HTTP_PARTIAL_CONTENT);
        }

        return parent::start($request, $authException);
    }

    public function supportsRememberMe()
    {
        return true;
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('login');
    }
}
