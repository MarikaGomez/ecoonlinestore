<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'label' => 'Current password',
                'attr' => [
                    'placeholder' => 'Write your current password',
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'constraints' => new Length([],8),
                'invalid_message' => 'Passwords do not match, try again !',
                'label' => 'New Password',
                'required' => true,
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => [
                        'placeholder' => 'Choose a new password',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm New Password',
                    'attr' => [
                        'placeholder' => 'Confirm your new password',
                    ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update Password',
                'attr' => [
                    'class' => 'btn-block btn-outline-danger mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
