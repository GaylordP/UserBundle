<?php

namespace GaylordP\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'label.email',
                'ico' => 'fas fa-envelope',
                'help' => 'email.help_privacy',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'label.password',
                'ico' => 'fas fa-key',
                'help' => 'password.help_case',
            ])
            ->add('remember_me', CheckboxType::class, [
                'label' => 'label.remember_me',
                'label_attr' => [
                    'class' => 'checkbox-custom',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'authenticate',
            'translation_domain' => 'user',
        ]);
    }
}
