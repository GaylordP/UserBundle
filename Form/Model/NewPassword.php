<?php

namespace GaylordP\UserBundle\Form\Model;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class NewPassword
{
    /**
     * @var User
     *
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     */
    private $password;

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

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set password
     * 
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
