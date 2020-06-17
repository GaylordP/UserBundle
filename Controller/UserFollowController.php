<?php

namespace GaylordP\UserBundle\Controller;

use App\Entity\User;
use GaylordP\UserBundle\Entity\UserFollow;
use GaylordP\UserBundle\Entity\UserNotification;
use GaylordP\UserBundle\Provider\UserProvider;
use GaylordP\UserBundle\Repository\UserFollowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        User $member,
        UserProvider $userProvider,
        PublisherInterface $publisher,
        TranslatorInterface $translator
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

            $notification = $entityManager->getRepository(UserNotification::class)->findOneBy([
                'type' => 'user_follow',
                'elementId' => $findFolow->getId(),
            ]);

            $notification->setDeletedBy($findFolow->getDeletedBy());
            $notification->setDeletedAt($findFolow->getDeletedAt());

            $entityManager->flush();

            if (!$request->isXmlHttpRequest()) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    [
                        'user.unfollow_successfully',
                        [
                            '%link_profile%' => $this->renderView('@User/button/_user.html.twig', [
                                'user' => $member,
                            ]),
                        ],
                        'user'
                    ]
                );
            }
        } else {
            $userFollow = new UserFollow();
            $userFollow->setUser($member);

            $entityManager->persist($userFollow);
            $entityManager->flush();

            $userNotification = new UserNotification();
            $userNotification->setUser($member);
            $userNotification->setType('user_follow');
            $userNotification->setElementId($userFollow->getId());

            $entityManager->persist($userNotification);
            $entityManager->flush();

            if (!$request->isXmlHttpRequest()) {
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
        }

        if ($request->isXmlHttpRequest()) {
            $userProvider->addExtraInfos($member);

            $update = new Update(
                'https://bubble.lgbt/user/' . $this->getUser()->getSlug(),
                json_encode([
                    'user-slug' => $member->getSlug(),
                    'isFollowed' => null !== $findFolow ? false : true,
                    'ico' => $this->renderView('@User/user/follow/_' . (null !== $findFolow ? 'follow' : 'unfollow') . '_ico.html.twig'),
                    'title' => null !== $findFolow ? $translator->trans('action.follow', [], 'user') : $translator->trans('action.unfollow', [], 'user'),
                ]),
                true,
                null,
                'user_follow'
            );
            $publisher($update);

            return new JsonResponse(null, Response::HTTP_OK);
        } else {
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
}
