<?php

namespace GaylordP\UserBundle\Validator;

use GaylordP\UserBundle\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserEmailValidator extends ConstraintValidator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($entity, Constraint $constraint): void
    {
        if (null !== $entity->getEmail()) {
            $user = $this->userRepository->findOneByEmail($entity->getEmail());

            if (null === $user) {
                $this
                    ->context
                    ->buildViolation($constraint->message)
                    ->setParameter('%email%', $entity->getEmail())
                    ->setTranslationDomain('security')
                    ->addViolation()
                ;
            } else {
                $entity->setUser($user);
            }
        }
    }
}
