<?php

namespace GaylordP\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GaylordP\UserBundle\Annotation\CreatedAt;
use GaylordP\UserBundle\Annotation\CreatedBy;
use GaylordP\UserBundle\Entity\Traits\Deletable;
use GaylordP\UserBundle\Entity\Traits\Validable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserForgotPassword
 *
 * @ORM\Entity(repositoryClass="GaylordP\UserBundle\Repository\UserForgotPasswordRepository")
 */
class UserForgotPassword
{
    use Validable;
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
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=36)
     * @Assert\NotBlank()
     */
    private $token;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @CreatedBy
     */
    private $createdBy;

    /**
     * Get name
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->getToken();
    }

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
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
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
