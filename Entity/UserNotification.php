<?php

namespace GaylordP\UserBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use GaylordP\UserBundle\Annotation\CreatedAt;
use GaylordP\UserBundle\Annotation\CreatedBy;
use GaylordP\UserBundle\Entity\Traits\Deletable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserNotification
 *
 * @ORM\Entity(repositoryClass="GaylordP\UserBundle\Repository\UserNotificationRepository")
 */
class UserNotification
{
    use Deletable;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User",
     *     fetch="EAGER"
     * )
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $parameters;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @CreatedAt
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User",
     *     fetch="EAGER"
     * )
     * @CreatedBy
     */
    private $createdBy;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?string
    {
        return $this->id;
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
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType(?string $type)
    {
        $this->type = $type;
    }

    /**
     * Get parameters
     *
     * @return string
     */
    public function getParameters(): ?string
    {
        return $this->parameters;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     */
    public function setParameters(?string $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $date
     */
    public function setCreatedAt(\DateTime $date): void
    {
        $this->createdAt = $date;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param User $user
     */
    public function setCreatedBy(User $user): void
    {
        $this->createdBy = $user;
    }
}
