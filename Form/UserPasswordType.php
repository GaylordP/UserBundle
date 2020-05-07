<?php

namespace GaylordP\UserBundle\Form;

use GaylordP\UserBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'always_empty' => false,
                'label' => 'label.password_current',
                'help' => 'password.help_case',
            ])
            ->add('newPassword', PasswordType::class, [
                'always_empty' => false,
                'label' => 'label.password_new',
                'help' => 'password.help_case',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
            'translation_domain' => 'user',
        ]);
    }
}
