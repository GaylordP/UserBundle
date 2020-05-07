<?php

namespace GaylordP\UserBundle\Entity\Traits;

use App\Entity\User;

trait Deletable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected $deletedBy;

    /**
     * Get deleted at
     * 
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set deleted at
     * 
     * @param \DateTime $date
     */
    public function setDeletedAt(?\DateTime $date): void
    {
        $this->deletedAt = $date;
    }

    /**
     * Get deleted by
     * 
     * @return User
     */
    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    /**
     * Set deleted by
     * 
     * @param User $user
     */
    public function setDeletedBy(?User $user): void
    {
        $this->deletedBy = $user;
    }
}
