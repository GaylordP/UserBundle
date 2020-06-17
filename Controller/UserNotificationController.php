<?php

namespace GaylordP\UserBundle\Controller;

use GaylordP\UserBundle\Repository\UserNotificationRepository;
use GaylordP\UserBundle\UserNotificationFormat\UserNotificationFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        UserNotificationFormat $notificationFormat,
        UserNotificationRepository $userNotificationRepository
    ): Response {
        $notifications = $userNotificationRepository->findAllPaginatedByUser(
            $this->getUser(),
            $page
        );

        $notificationFormat->format($notifications->getResults());

        return $this->render('@User/notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
