<?php

namespace GaylordP\UserBundle\Form\Model;

use App\Entity\User;
use GaylordP\UserBundle\Validator as UserAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UserAssert\UserEmail()
 */
class UserEmail
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var User
     */
    private $user;

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email
     * 
     * @param string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = strtolower($email);
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
