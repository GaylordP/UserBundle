<?php

namespace GaylordP\UserBundle\Controller;

use GaylordP\PaginatorBundle\Twig\Extension;
use GaylordP\UserBundle\Repository\UserNotificationRepository;
use GaylordP\UserBundle\UserNotificationFormat\UserNotificationFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserNotificationController extends AbstractController
{
    /**
     * @Route(
     *     {
     *         "fr": "/user/notification",
     *     },
     *     name="user_notification",
     *     defaults=
     *     {
     *         "page": "1",
     *     },
     *     methods="GET"
     * )
     * @Route(
     *     {
     *         "fr": "/user/notification/{page}",
     *     },
     *     requirements=
     *     {
     *         "page": "[1-9]\d*",
     *     },
     *     name="user_notification_paginated",
     *     methods="GET"
     * )
     */
    public function index(
        int $page = 1,
        Request $request,
        UserNotificationFormat $notificationFormat,
        UserNotificationRepository $userNotificationRepository,
        Extension $paginatorTwigExtension
    ): Response {
        $notifications = $userNotificationRepository->findAllPaginatedByUser(
            $this->getUser(),
            $page
        );

        $paginatorTwigExtension->setHeadPaginator($notifications);

        if (
            (true === $notifications->hasToPaginate() && $notifications->getCurrentPage() > $notifications->getNbPages())
                ||
            (false === $notifications->hasToPaginate() && 1 !== $notifications->getCurrentPage())
        ) {
            return $this->redirectToRoute('user_notification');
        }

        $notificationFormat->format($notifications->getResults());

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('@User/notification/_list.html.twig', [
                    'notifications' => $notifications,
                ]),
            ], Response::HTTP_OK);
        }

        return $this->render('@User/notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
