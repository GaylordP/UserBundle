<?php

namespace GaylordP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserNotificationReadController extends AbstractController
{
    /**
     * @Route(
     *     {
     *         "fr": "/user/notification-read",
     *     },
     *     name="notification_read",
     *     methods="GET"
     * )
     */
    public function notificationRead(): JsonResponse
    {
        $this->getUser()->setNotificationReadAt(new \DateTime());

        $this
            ->getDoctrine()
            ->getManager()
            ->flush()
        ;

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
