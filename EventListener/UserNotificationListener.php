<?php

namespace GaylordP\UserBundle\EventListener;

use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use GaylordP\UserBundle\Entity\UserNotification;
use GaylordP\UserBundle\Repository\UserNotificationRepository;
use GaylordP\UserBundle\UserNotificationFormat\UserNotificationFormat;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class UserNotificationListener
{
    private $userNotificationRepository;
    private $userNotificationFormat;
    private $publisher;
    private $twig;
    private $security;

    private $refreshUserNotificationUnread = false;

    public function __construct(
        UserNotificationRepository $userNotificationRepository,
        UserNotificationFormat $userNotificationFormat,
        PublisherInterface $publisher,
        Environment $twig,
        Security $security
    ) {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->userNotificationFormat = $userNotificationFormat;
        $this->publisher = $publisher;
        $this->twig = $twig;
        $this->security = $security;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof UserNotification) {
            $this->userNotificationFormat->format($entity);

            $update = new Update(
                'https://bubble.lgbt/user/' . $entity->getUser()->getSlug(),
                json_encode([
                    'notificationHtml' => $this->twig->render('@User/notification/_notification.html.twig', [
                        'notification' => $entity,
                    ]),
                    'notificationNavbarHtml' => $this->twig->render('@User/notification/_notification.html.twig', [
                        'notification' => $entity,
                        'is_navbar' => true,
                    ])
                ]),
                true,
                null,
                'user_notification'
            );

            $publisher = $this->publisher;
            $publisher($update);

            $this->refreshUserNotificationUnread = $entity->getUser();
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (
                $entity instanceof UserNotification
                    &&
                array_key_exists('deletedAt', $uow->getEntityChangeSet($entity))
            ) {
                $update = new Update(
                    'https://bubble.lgbt/user/' . $entity->getUser()->getSlug(),
                    json_encode([
                        'id' => $entity->getId(),
                    ]),
                    true,
                    null,
                    'user_notification_delete'
                );

                $publisher = $this->publisher;
                $publisher($update);

                $this->refreshUserNotificationUnread = $entity->getUser();
            } elseif (
                $entity instanceof User
                    &&
                array_key_exists('notificationReadAt', $uow->getEntityChangeSet($entity))
            ) {
                $this->refreshUserNotificationUnread = $entity;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->refreshUserNotificationUnread instanceof User) {
            $update = new Update(
                'https://bubble.lgbt/user/' . $this->refreshUserNotificationUnread->getSlug(),
                json_encode([
                    'length' => $this->userNotificationRepository->countUnread($this->refreshUserNotificationUnread),
                ]),
                true,
                null,
                'user_notification_unread_length'
            );

            $publisher = $this->publisher;
            $publisher($update);

            $this->refreshUserNotificationUnread = false;
        }
    }
}
