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

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => new Length([],2),
                'attr' => [
                    'placeholder' => 'Firstname',
                ]
            ])
            ->add('lastname', TextType::class, [
                'constraints' => new Length([],2),
                'attr' => [
                    'placeholder' => 'Lastname',
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => new Length([],2,60),
                'attr' => [
                    'placeholder' => 'Email',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => new Length([],8),
                'invalid_message' => 'Passwords do not match, try again !',
                'label' => 'Password',
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder' => 'Password',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'attr' => [
                        'placeholder' => 'Confirm Password',
                    ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register',
                'attr' => [
                    'class' => 'btn-block btn-outline-danger'
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