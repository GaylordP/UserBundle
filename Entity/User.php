<?php

namespace GaylordP\UserBundle\Entity;

use App\Entity\UserMedia;
use Doctrine\ORM\Mapping as ORM;
use GaylordP\ColorBundle\Entity\Color;
use GaylordP\SluggableBundle\Annotation\Sluggable;
use GaylordP\UserBundle\Annotation\CreatedAt;
use GaylordP\UserBundle\Annotation\CreatedBy;
use GaylordP\UserBundle\Entity\Traits\Deletable;
use GaylordP\UserBundle\Entity\Traits\Validable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\MappedSuperclass
 * @ORM\Table("app_user")
 * @UniqueEntity(
 *     "email",
 *     message = "email.already_taken",
 *     repositoryMethod = "findUniqueEntityByEmail"
 * )
 */
class User implements UserInterface, \Serializable
{
    use Deletable;
    use Validable;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $roles = [];

    /**
     * @var UserMedia
     *
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\UserMedia",
     *     cascade={"persist"},
     *     fetch="EAGER"
     * )
     */
    protected $userMedia;

    /**
     * @var Color
     *
     * @ORM\ManyToOne(
     *     targetEntity="GaylordP\ColorBundle\Entity\Color",
     *     fetch="EAGER"
     * )
     * @Assert\NotBlank()
     */
    protected $color;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=36)
     */
    protected $validationToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $onlineAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Sluggable("username")
     */
    protected $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @CreatedAt
     */
    protected $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @CreatedBy
     */
    protected $createdBy;

    public function __construct()
    {
        $this->setOnlineAt(new \DateTime());
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->getUsername();
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
     * Get username
     *
     * @return string 
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set username
     * 
     * @param string $username
     */
    public function setUsername(?string $username)
    {
        $this->username = $username;
    }

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
    public function setEmail(?string $email)
    {
        $this->email = strtolower($email);
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
    public function setPassword(?string $password)
    {
        $this->password = $password;
    }

    /**
     * Get roles
     * 
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set roles
     * 
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Get user media (profile media)
     * 
     * @return UserMedia
     */
    public function getUserMedia(): ?UserMedia
    {
        return $this->userMedia;
    }

    /**
     * Set user media (profile media)
     * 
     * @param UserMedia $userMedia
     */
    public function setUserMedia(?UserMedia $userMedia): void
    {
        $this->userMedia = $userMedia;
    }

    /**
     * Get color
     *
     * @return Color
     */
    public function getColor(): ?Color
    {
        return $this->color;
    }

    /**
     * Set color
     *
     * @param Color $color
     */
    public function setColor(?Color $color): void
    {
        $this->color = $color;
    }

    /**
     * Get content (user description)
     *
     * @return string 
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set content (user description)
     * 
     * @param string $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Get validation token
     *
     * @return string
     */
    public function getValidationToken(): ?string
    {
        return $this->validationToken;
    }

    /**
     * Set validation token
     *
     * @param string $validationToken
     */
    public function setValidationToken(?string $validationToken): void
    {
        $this->validationToken = $validationToken;
    }

    /**
     * Get online at
     * 
     * @return \DateTime
     */
    public function getOnlineAt(): ?\DateTime
    {
        return $this->onlineAt;
    }

    /**
     * Set online at
     * 
     * @param \DateTime $date
     */
    public function setOnlineAt(\DateTime $date): void
    {
        $this->onlineAt = $date;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug
     * 
     * @param string $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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

    /*
     * Get salt
     * 
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    /**
     * Get serialize : [id, username, email, password]
     * 
     * @return string 
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->email, $this->password]);
    }

    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->email, $this->password] = unserialize($serialized, [
            'allowed_classes' => false
        ]);
    }
}
