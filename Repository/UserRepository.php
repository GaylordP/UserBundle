<?php

namespace GaylordP\UserBundle\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUniqueEntityByEmail(array $data): ?User
    {
        $this->getEntityManager()->getFilters()->disable('deleted_at');

        $findOneBy = $this->findOneByEmail($data['email']);

        $this->getEntityManager()->getFilters()->enable('deleted_at');

        return $findOneBy;
    }

    public function findUniqueEntityByUsername(array $data): ?User
    {
        $this->getEntityManager()->getFilters()->disable('deleted_at');

        $findOneBy = $this->findOneByUsername($data['username']);

        $this->getEntityManager()->getFilters()->enable('deleted_at');

        return $findOneBy;
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
