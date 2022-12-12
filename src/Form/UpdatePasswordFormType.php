<?php

namespace App\Form;

use App\Form\Model\UpdatePassword;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Mot de passe actuel',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe actuel.',
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Nouveau mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmation du nouveau mot de passe',
                    ],
                ],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nouveau mot de passe.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => UpdatePassword::class,
            'csrf_token_id' => 'update_password',
        ]);
    }
}