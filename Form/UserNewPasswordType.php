<?php

namespace GaylordP\UserBundle\Form;

use GaylordP\UserBundle\Form\Model\NewPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserNewPasswordType extends AbstractType
{
    private $encoder;
    private $translator;

    public function __construct(UserPasswordEncoderInterface $encoder, TranslatorInterface $translator)
    {
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'label.password',
                'always_empty' => false,
                'ico' => 'fas fa-key',
                'help' => 'password.help_case',
            ])
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [
                    $this,
                    'onPostSubmit',
                ]
            )
        ;
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null !== $data->getPassword() && true === $this
            ->encoder
            ->isPasswordValid($data->getUser(), $data->getPassword())
        ) {
            $form->get('password')->addError(
                new FormError(
                    $this->translator->trans('password.updated_same', [], 'validators')
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewPassword::class,
            'translation_domain' => 'user',
        ]);
    }
}
