<?php

namespace GaylordP\UserBundle\UserNotificationFormat;

use GaylordP\UserBundle\Entity\UserNotification;
use GaylordP\UserBundle\Repository\UserFollowRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserNotificationFormat implements UserNotificationFormatInterface
{
    private $translator;
    private $twig;
    private $router;
    private $userFollowRepository;
    private $notificationsFormat = [];

    public function __construct(
        TranslatorInterface $translator,
        Environment $twig,
        RouterInterface $router,
        UserFollowRepository $userFollowRepository
    ) {
        $this->translator = $translator;
        $this->twig = $twig;
        $this->router = $router;
        $this->userFollowRepository = $userFollowRepository;
    }

    public function addUserNotificationFormat(UserNotificationFormatInterface $notificationFormat): void
    {
        $this->notificationsFormat[] = $notificationFormat;
    }

    public function format($notifications): array
    {
        if ($notifications instanceof \ArrayIterator) {
            $notifications = iterator_to_array($notifications);
        }

        if ($notifications instanceof UserNotification) {
            $notifications = [
                $notifications
            ];
        }

        foreach ($this->notificationsFormat as $notificationFormat) {
            $notifications = $notificationFormat->format($notifications);
        }

        $this->userBundleNotificationFormat($notifications);

        return $notifications;
    }

    private function userBundleNotificationFormat(array $notifications): void
    {
        $this->user_follow($notifications);
        $this->user_password_forgot($notifications);
        $this->user_password_update($notifications);
        $this->user_register($notifications);
        $this->user_register_validation($notifications);
    }

    private function user_follow(array $notifications): void
    {
        $notificationsByFollowId = [];

        array_map(function($e) use(&$notificationsByFollowId) {
            if ('user_follow' === $e->getType()) {
                $notificationsByFollowId[$e->getElementId()] = $e;
            }
        }, $notifications);

        if (!empty($notificationsByFollowId)) {
            foreach ($this->userFollowRepository->findById(array_keys($notificationsByFollowId)) as $follow) {
                $notificationsByFollowId[$follow->getId()]->__color = $follow
                    ->getCreatedBy()
                    ->getColor()
                    ->getSlug()
                ;

                $notificationsByFollowId[$follow->getId()]->__link = $this
                    ->router
                    ->generate('member_profile', [
                        'slug' => $follow->getCreatedBy()->getSlug(),
                    ])
                ;

                $notificationsByFollowId[$follow->getId()]->__text = $this
                    ->twig
                    ->render('@User/notification/notification/_user_follow.html.twig', [
                        'follow' => $follow,
                    ])
                ;
            }
        }
    }

    private function user_password_forgot(array $notifications): void
    {
        array_map(function($e) use($notifications) {
            if ('user_password_forgot' === $e->getType()) {
                $e->__color = $e
                    ->getUser()
                    ->getColor()
                    ->getSlug()
                ;

                $e->__text = $this
                    ->twig
                    ->render('@User/notification/notification/_user_password_forgot.html.twig')
                ;
            }
        }, $notifications);
    }

    private function user_password_update(array $notifications): void
    {
        array_map(function($e) use($notifications) {
            if ('user_password_update' === $e->getType()) {
                $e->__color = $e
                    ->getUser()
                    ->getColor()
                    ->getSlug()
                ;

                $e->__text = $this
                    ->twig
                    ->render('@User/notification/notification/_user_password_update.html.twig')
                ;
            }
        }, $notifications);
    }

    private function user_register(array $notifications): void
    {
        array_map(function($e) use($notifications) {
            if ('user_register' === $e->getType()) {
                $e->__color = $e
                    ->getUser()
                    ->getColor()
                    ->getSlug()
                ;

                $e->__text = $this
                    ->twig
                    ->render('@User/notification/notification/_user_register.html.twig', [
                        'notification' => $e,
                    ])
                ;
            }
        }, $notifications);
    }

    private function user_register_validation(array $notifications): void
    {
        array_map(function($e) use($notifications) {
            if ('user_register_validation' === $e->getType()) {
                $e->__color = $e
                    ->getUser()
                    ->getColor()
                    ->getSlug()
                ;

                $e->__text = $this
                    ->twig
                    ->render('@User/notification/notification/_user_register_validation.html.twig', [
                        'notification' => $e,
                    ])
                ;
            }
        }, $notifications);
    }
}
