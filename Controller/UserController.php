<?php

namespace GaylordP\UserBundle\Controller;

use App\Form\UserInformationType;
use GaylordP\UserBundle\Form\Model\ChangePassword;
use GaylordP\UserBundle\Form\UserPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route(
     *     {
     *         "fr": "/user/information",
     *     },
     *     name="user_information",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function information(Request $request): Response
    {
        $cloneUser = clone $this->getUser();
        
        $form = $this->createForm(UserInformationType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    [
                        'user.information.updated_successfully',
                        [],
                        'user'
                    ],
                );

                return $this->redirectToRoute('user_information');
            } else {
                $this->getUser()->setUsername($cloneUser->getUsername());
                $this->getUser()->setEmail($cloneUser->getEmail());
            }
        }

        return $this->render('@User/user/information.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/user/password",
     *     },
     *     name="user_password",
     *     methods=
     *     {
     *         "GET",
     *         "POST",
     *     }
     * )
     */
    public function password(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder
    ): Response {
        $changePassword = new ChangePassword();
        
        $form = $this->createForm(UserPasswordType::class, $changePassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder
                ->encodePassword(
                    $this->getUser(),
                    $changePassword->getNewPassword()
                )
            ;

            $this->getUser()->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                [
                    'user.password.updated_successfully',
                    [],
                    'user'
                ],
            );

            return $this->redirectToRoute('user_password');
        }

        return $this->render('@User/user/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
