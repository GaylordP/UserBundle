<?php

namespace GaylordP\UserBundle\Validator;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserPasswordOldNewSameValidator extends ConstraintValidator
{
    private $security;
    private $encoder;

    public function __construct(Security $security, UserPasswordEncoderInterface $encoder)
    {
        $this->security = $security;
        $this->encoder = $encoder;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (null !== $value && true === $this
            ->encoder
            ->isPasswordValid($this->security->getUser(), $value)
        ) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation()
            ;
        }
    }
}
