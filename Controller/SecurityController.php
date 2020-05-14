<?php

namespace GaylordP\UserBundle\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use GaylordP\UserBundle\Entity\UserForgotPassword;
use GaylordP\UserBundle\Form\LoginType;
use GaylordP\UserBundle\Form\Model\NewPassword;
use GaylordP\UserBundle\Form\Model\UserEmail;
use GaylordP\UserBundle\Form\UserEmailType;
use GaylordP\UserBundle\Form\UserNewPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route(
     *     {
     *         "fr": "/connexion",
     *     },
     *     name="login",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        TranslatorInterface $translator
    ): Response {
        if (null !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                $translator->trans('login.logged', [], 'user')
            );
        }

        $form = $this->createForm(LoginType::class);

        return $this->render('@User/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/deconnexion",
     *     },
     *     name="logout",
     *     methods="GET"
     * )
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route(
     *     {
     *         "fr": "/inscription",
     *     },
     *     name="register",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TranslatorInterface $translator,
        MailerInterface $mailer
    ): Response {
        if (null !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                $translator->trans('login.logged', [], 'user')
            );
        }

        $user = new User();
        $user->setRoles([
            'ROLE_USER',
        ]);        

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /*
             * User and flush
             */
            $password = $userPasswordEncoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);
            $user->setValidationToken(uuid_create(UUID_TYPE_RANDOM));

            $entityManager->persist($user);
            $entityManager->flush();

            /*
             * Email
             */
            $this->sendEmailRegister($user, $mailer);

            /*
             * Session
             */
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);

            $this->get('session')->getFlashBag()->add(
                'success',
                [
                    'user.registered_successfully',
                    [
                        '%username%' => $this->renderView('@User/button/_user.html.twig', [
                            'user' => $user,
                        ]),
                    ],
                    'user'
                ]
            );

            return $this->redirectToRoute($this->getParameter('form_login_default_target_path'));
        }

        return $this->render('@User/security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/lien-de-validation-perdu",
     *     },
     *     name="forgot_register_validation",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function forgotRegisterValidation(
        Request $request,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {
        $userEmail = new UserEmail();

        $form = $this->createForm(UserEmailType::class, $userEmail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $userEmail->getUser()->getValidatedAt()) {
                $form->get('email')->addError(
                    new FormError(
                        $translator->trans(
                            'user.email_already_validated',
                            [
                                '%email%' => $userEmail->getUser()->getEmail(),
                            ],
                            'user'
                        )
                    )
                );
            }

            if ($form->isValid()) {
                $this->sendEmailRegister($userEmail->getUser(), $mailer);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    [
                        'user.registered_token',
                        [
                            '%email%' => $userEmail->getUser()->getEmail(),
                        ],
                        'user'
                    ]
                );

                return $this->redirectToRoute('forgot_register_validation');
            }
        }

        return $this->render('@User/security/forgot_register_validation.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/inscription-validation/{validationToken}",
     *     },
     *     name="register_validation",
     *     methods="GET"
     * )
     */
    public function registerValidation(User $user, TranslatorInterface $translator): Response
    {
        if (null !== $user->getValidatedAt()) {
            throw $this->createNotFoundException(
                $translator->trans(
                    'user.email_already_validated',
                    [
                        '%email%' => $user->getEmail(),
                    ],
                    'user'
                )
            );
        }

        $user->setValidatedAt(new \DateTime());
        $user->setValidatedBy($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);

        $this->get('session')->getFlashBag()->add(
            'success',
            [
                'user.validation_successfully',
                [
                    '%email%' => $user->getEmail(),
                ],
                'user'
            ]
        );

        return $this->redirectToRoute($this->getParameter('form_login_default_target_path'));
    }

    /**
     * @Route(
     *     {
     *         "fr": "/mot-de-passe-oublie",
     *     },
     *     name="forgot_password",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function forgotPassword(Request $request, MailerInterface $mailer): Response
    {
        $userEmail = new UserEmail();

        $form = $this->createForm(UserEmailType::class, $userEmail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->isValid()) {
                /*
                 * Remove lasts token
                 */
                $lasts = $entityManager
                    ->getRepository(UserForgotPassword::class)
                    ->findBy([
                        'user' => $userEmail->getUser(),
                        'validatedAt' => null,
                    ])
                ;

                foreach ($lasts as $last) {
                    $last->setDeletedAt(new \DateTime());
                    $last->setDeletedBy($userEmail->getUser());
                }

                /*
                 * Create new token
                 */
                $forgot = new UserForgotPassword();
                $forgot->setUser($userEmail->getUser());
                $forgot->setToken(uuid_create(UUID_TYPE_RANDOM));

                /*
                 * Persist & flush
                 */
                $entityManager->persist($forgot);
                $entityManager->flush();

                /*
                 * Send e-mail
                 */
                $emailTitle = 'Mot de passe oublié | Bubble.lgbt';

                $email = (new TemplatedEmail())
                    ->to($userEmail->getUser()->getEmail())
                    ->subject($emailTitle)
                    ->htmlTemplate('@User/email/security/forgot_password.html.twig')
                    ->context([
                        'title' => $emailTitle,
                        'forgot' => $forgot,
                    ])
                ;

                $mailer->send($email);

                /*
                 * FlashBag
                 */
                $this->get('session')->getFlashBag()->add(
                    'success',
                    [
                        'user.password.forgot_send',
                        [
                            '%email%' => $userEmail->getUser()->getEmail(),
                        ],
                        'user'
                    ]
                );

                /*
                 * Response
                 */
                return $this->redirectToRoute('forgot_password');
            }
        }

        return $this->render('@User/security/forgot_password.html.twig', [
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/mot-de-passe-oublie/{_token}",
     *     },
     *     name="forgot_password_token",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     * @Entity("forgot", expr="repository.findOneBy({ token: token, validatedAt: null })")
     */
    public function forgotPasswordToken(
        Request $request,
        UserForgotPassword $forgot,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $password = new NewPassword();
        $password->setUser($forgot->getUser());

        $form = $this->createForm(UserNewPasswordType::class, $password);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*
             * Update database password
             */
            $password = $userPasswordEncoder->encodePassword($forgot->getUser(), $password->getPassword());

            $forgot->getUser()->setPassword($password);

            /*
             * Update database token
             */
            $entityManager = $this->getDoctrine()->getManager();

            $forgot->setValidatedAt(new \DateTime());
            $forgot->setValidatedBy($forgot->getUser());

            $entityManager->flush();

            /*
             * Auto logged user
             */
            $token = new UsernamePasswordToken($forgot->getUser(), null, 'main', $forgot->getUser()->getRoles());
            $this->container->get('security.token_storage')->setToken($token);

            /*
             * FlashBag
             */
            $this->get('session')->getFlashBag()->add(
                'success',
                [
                    'user.password.new_successfully',
                    [],
                    'user'
                ],
            );

            /*
             * Response
             */
            return $this->redirectToRoute($this->getParameter('form_login_default_target_path'));
        }

        return $this->render('@User/security/forgot_password_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendEmailRegister(User $user, MailerInterface $mailer): void
    {
        $emailTitle = 'Bienvenue | Bubble.lgbt';

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject($emailTitle)
            ->htmlTemplate('@User/email/security/register.html.twig')
            ->context([
                'title' => $emailTitle,
                'user' => $user,
            ])
        ;

        $mailer->send($email);
    }
}
