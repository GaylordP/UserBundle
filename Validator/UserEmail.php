<?php

namespace GaylordP\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserEmail extends Constraint
{
    public $message = 'user.email.unexist';

    public function getTargets()
    {
        return parent::CLASS_CONSTRAINT;
    }
}
