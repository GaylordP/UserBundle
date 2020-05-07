<?php

namespace GaylordP\UserBundle\Form;

use GaylordP\UserBundle\Form\Model\UserEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'label.email',
                'ico' => 'fas fa-envelope',
                'help' => 'email.help_privacy',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEmail::class,
            'translation_domain' => 'user',
        ]);
    }
}
