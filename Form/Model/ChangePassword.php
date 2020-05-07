<?php

namespace GaylordP\UserBundle\Form\Model;

use GaylordP\UserBundle\Validator as UserAssert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @SecurityAssert\UserPassword(
     *     message = "user.password.wrong"
     * )
     */
    private $oldPassword;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     * @UserAssert\UserPasswordOldNewSame()
     */
    private $newPassword;
    
    /**
     * Get oldPassword
     *
     * @return string 
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * Set oldPassword
     * 
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * Get newPassword
     *
     * @return string 
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * Set newPassword
     * 
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }
}
