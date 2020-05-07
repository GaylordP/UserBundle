<?php

namespace GaylordP\UserBundle\Entity\Traits;

use App\Entity\User;

trait Validable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $validatedBy;

    /**
     * Get validated at
     * 
     * @return \DateTime
     */
    public function getValidatedAt(): ?\DateTime
    {
        return $this->validatedAt;
    }

    /**
     * Set validated at
     * 
     * @param \DateTime $date
     */
    public function setValidatedAt(?\DateTime $date): void
    {
        $this->validatedAt = $date;
    }

    /**
     * Get validated by
     * 
     * @return User
     */
    public function getValidatedBy(): ?User
    {
        return $this->validatedBy;
    }

    /**
     * Set validated by
     * 
     * @param User $user
     */
    public function setValidatedBy(?User $user): void
    {
        $this->validatedBy = $user;
    }
}
