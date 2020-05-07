<?php

namespace GaylordP\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserPasswordOldNewSame extends Constraint
{
    public $message = 'password.updated_same';
}
