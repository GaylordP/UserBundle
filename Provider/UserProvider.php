<?php

namespace GaylordP\UserBundle\Provider;

use App\Entity\User;
use GaylordP\UserBundle\Repository\UserFollowRepository;
use Symfony\Component\Security\Core\Security;

class UserProvider
{
    const IS_USER_FOLLOWED = '__isUserFollowed';

    protected $security;
    protected $userFollowRepository;

    public function __construct(
        Security $security,
        UserFollowRepository $userFollowRepository
    ) {
        $this->security = $security;
        $this->userFollowRepository = $userFollowRepository;
    }

    public function addExtraInfos($user)
    {
        $listEntitiesById = [];

        if ($user instanceof User) {
            $listEntitiesById[$user->getId()] = $user;
        } elseif (is_array($user) && current($user) instanceof User) {
            array_map(function($e) use(&$listEntitiesById) {
                $listEntitiesById[$e->getId()] = $e;
            }, $user);
        }

        if (!empty($listEntitiesById)) {
            /*
             * UserFollow
             */
            if (null !== $this->security->getUser()) {
                $followedsIds = array_map(function($e) {
                    if (false === property_exists($e, self::IS_USER_FOLLOWED)) {
                        return $e->getId();
                    }
                }, $listEntitiesById);

                $followeds = $this->userFollowRepository->findBy([
                    'createdBy' => $this->security->getUser(),
                    'user' => $followedsIds,
                ]);

                foreach ($followeds as $followed) {
                    $listEntitiesById[$followed->getUser()->getId()]->{self::IS_USER_FOLLOWED} = true;
                }
            }

            /*
             * Default
             */
            foreach ($listEntitiesById as $entity) {
                if (false === property_exists($entity, self::IS_USER_FOLLOWED)) {
                    $entity->{self::IS_USER_FOLLOWED} = false;
                }
            }
        }

        return $user;
    }
}
