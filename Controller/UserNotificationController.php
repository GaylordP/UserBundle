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

        dd($notificationFormat->format(iterator_to_array($notifications->getResults())));
    }
}
