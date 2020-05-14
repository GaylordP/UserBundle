<?php

namespace GaylordP\UserBundle\Provider;

use App\Entity\User;
use GaylordP\UserBundle\Repository\UserFollowRepository;
use Symfony\Component\Security\Core\Security;

class UserProvider
{
    const IS_USER_FOLLOWED = '__isUserFollowed';

    private $security;
    private $userFollowRepository;

    public function __construct(
        Security $security,
        UserFollowRepository $userFollowRepository
    ) {
        $this->security = $security;
        $this->userFollowRepository = $userFollowRepository;
    }

    public function addExtraInfos(
        $user,
        bool $isUserFollowed = false
    ) {
        $ids = [];
        $listEntitiesById = [];

        if ($user instanceof User) {
            $listEntitiesById[$user->getId()] = $user;
            $ids[] = $user->getId();
        } elseif (is_array($user) && current($user) instanceof User) {
            $ids = array_map(function($e) use(&$listEntitiesById) {
                $listEntitiesById[$e->getId()] = $e;

                return $e->getId();
            }, $user);
        }

        if (!empty($ids)) {
            if (true === $isUserFollowed && null !== $this->security->getUser()) {
                $followeds = $this->userFollowRepository->findBy([
                    'createdBy' => $this->security->getUser(),
                    'user' => $ids,
                ]);

                foreach ($followeds as $followed) {
                    $listEntitiesById[$followed->getUser()->getId()]->{self::IS_USER_FOLLOWED} = true;
                }
            }

            foreach ($listEntitiesById as $entity) {
                if (true === $isUserFollowed && false === property_exists($entity, self::IS_USER_FOLLOWED)) {
                    $entity->{self::IS_USER_FOLLOWED} = false;
                }
            }
        }

        return $user;
    }
}
