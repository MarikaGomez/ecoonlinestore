<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
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
            ->add('subject', TextType::class, [
                'constraints' => new Length([],2,60),
                'attr' => [
                    'placeholder' => 'Subject',
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'placeholder' => 'Message'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-block btn-outline-danger'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
