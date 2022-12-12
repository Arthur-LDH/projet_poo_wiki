<?php

namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints\NotIdenticalTo;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UpdatePassword
{
    #[SecurityAssert\UserPassword(
        message: 'Le mot de passe actuel est incorrect.',
    )]
    protected ?string $oldPassword;
    #[NotIdenticalTo(
        propertyPath: 'oldPassword',
        message: 'Le nouveau mot de passe doit être différent de l\'ancien.',
    )]
    protected ?string $newPassword;

    function __construct()
    {
        $this->oldPassword = null;
        $this->newPassword = null;
    }

    function getOldPassword() : ?string
    {
        return $this->oldPassword;
    }

    function setOldPassword(?string $oldPassword) : void
    {
        $this->oldPassword = $oldPassword;
    }

    function getNewPassword() : ?string
    {
        return $this->newPassword;
    }

    function setNewPassword(?string $newPassword) : void
    {
        $this->newPassword = $newPassword;
    }
}