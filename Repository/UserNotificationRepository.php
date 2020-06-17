<?php

namespace GaylordP\UserBundle\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use GaylordP\PaginatorBundle\Paginator;
use GaylordP\UserBundle\Entity\UserNotification;

class UserNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotification::class);
    }

    public function findAllPaginatedByUser(User $user, int $page = 1): Paginator
    {
        $qb = $this
            ->createQueryBuilder('notification')
            ->andWhere('notification.user = :user')
            ->setParameter('user', $user)
            ->select('
                notification
            ')
            ->orderBy('notification.id', 'DESC')
        ;

        return (new Paginator($qb))->paginate($page);
    }

    public function countUnread(User $user): int
    {
        return $this
            ->createQueryBuilder('notification')
            ->andWhere('notification.user = :user')
            ->setParameter('user', $user)
            ->andWhere('notification.createdAt > :notificationReadAt')
            ->setParameter('notificationReadAt', $user->getNotificationReadAt())
            ->select('
                COUNT(notification.id)
            ')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
