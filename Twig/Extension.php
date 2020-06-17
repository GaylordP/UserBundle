<?php

namespace GaylordP\UserBundle\Twig;

use GaylordP\PaginatorBundle\Paginator;
use GaylordP\UserBundle\Entity\UserNotification;
use GaylordP\UserBundle\Repository\UserNotificationRepository;
use GaylordP\UserBundle\UserNotificationFormat\UserNotificationFormat;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class Extension extends AbstractExtension
{
    private $security;
    private $twig;
    private $userNotificationFormat;
    private $userNotificationRepository;

    private $userNotification = null;
    private $countUserNotificationUnread = null;

    public function __construct(
        Security $security,
        Environment $twig,
        UserNotificationFormat $userNotificationFormat,
        UserNotificationRepository $userNotificationRepository
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->userNotificationFormat = $userNotificationFormat;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'user_notification',
                [$this, 'getUserNotification'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'count_user_notification_unread',
                [$this, 'countUserNotificationUnread'],
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest(
                'user_notification_read',
                [$this, 'userNotificationIsRead']
            ),
        ];
    }

    public function getUserNotification(): Paginator
    {
        if (null === $this->userNotification) {
            $this->userNotification = $this
                ->userNotificationRepository
                ->findAllPaginatedByUser($this->security->getUser(), 1)
            ;
        }

        $this->userNotificationFormat->format($this->userNotification->getResults());

        return $this->userNotification;
    }

    public function countUserNotificationUnread(): int
    {
        if (null === $this->countUserNotificationUnread) {
            $this->countUserNotificationUnread = $this
                ->userNotificationRepository
                ->countUnread($this->security->getUser())
            ;
        }

        return $this->countUserNotificationUnread;
    }

    public function userNotificationIsRead(UserNotification $userNotification): bool
    {
        return $userNotification->getCreatedAt() <= $userNotification->getUser()->getNotificationReadAt();
    }
}
