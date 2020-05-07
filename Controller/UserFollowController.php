<?php

namespace GaylordP\UserBundle\Controller;

use App\Entity\User;
use GaylordP\UserBundle\Entity\UserFollow;
use GaylordP\UserBundle\Repository\UserFollowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class UserFollowController extends AbstractController
{
    /**
     * @Route(
     *     {
     *         "fr": "/user/follow",
     *     },
     *     name="user_follow_list",
     *     methods="GET"
     * )
     */
    public function index(
        UserFollowRepository $userFollowRepository
    ): Response {
        $myFollowers = $userFollowRepository->findBy([
            'user' => $this->getUser(),
        ], [
            'id' => 'DESC',
        ]);

        $myFolloweds = $userFollowRepository->findBy([
            'createdBy' => $this->getUser(),
        ], [
            'id' => 'DESC',
        ]);

        return $this->render('@User/user/follow/index.html.twig', [
            'my_followers' => $myFollowers,
            'my_followeds' => $myFolloweds,
        ]);
    }

    /**
     * @Route(
     *     {
     *         "fr": "/user/@{slug}/follow",
     *     },
     *     name="user_follow",
     *     methods="GET"
     * )
     */
    public function follow(
        Request $request,
        RouterInterface $router,
        User $member
    ): Response {
        if ($this->getUser() === $member) {
            throw $this->createNotFoundException();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $findFolow = $entityManager->getRepository(UserFollow::class)->findOneBy([
            'createdBy' => $this->getUser(),
            'user' => $member,
        ]);

        if (null !== $findFolow) {
            $findFolow->setDeletedBy($this->getUser());
            $findFolow->setDeletedAt(new \DateTime());

            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                [
                    'user.unfollow_successfully',
                    [
                        '%link_profile%' => $this->renderView('@User/button/_user.html.twig', [
                            'user' => $member,
                        ]),
                        '%username%' => '<span class="btn btn-sm btn-' . $member->getColor()->getSlug() . '">@' . $member->getUsername() .'</span>',
                    ],
                    'user'
                ]
            );
        } else {
            $userFollow = new UserFollow();
            $userFollow->setUser($member);

            $entityManager->persist($userFollow);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                [
                    'user.follow_successfully',
                    [
                        '%link_profile%' => $this->renderView('@User/button/_user.html.twig', [
                            'user' => $member,
                        ]),
                    ],
                    'user'
                ]
            );
        }

        if (
            null !== $request->headers->get('referer')
                &&
            'login' !== $router->match(parse_url($request->headers->get('referer'))['path'])['_route']
        ) {
            return $this->redirect($request->headers->get('referer'));
        } else {
            return $this->redirectToRoute('member_profile', [
                'slug' => $member->getSlug(),
            ]);
        }
    }
}
