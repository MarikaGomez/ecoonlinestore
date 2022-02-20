<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Give a name to your address',
                'attr' => [
                    'placeholder' => 'Home',
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Firstname',
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Lastname',
                ]
            ])
            ->add('company', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Optional...',
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'placeholder' => 'Address',
                ]
            ])
            ->add('zip_code', TextType::class, [
                'attr' => [
                    'placeholder' => 'Zip Code',
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => 'City',
                ]
            ])
            ->add('country', CountryType::class, [
                'attr' => [
                    'placeholder' => 'Country',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone Number',
                'attr' => [
                    'placeholder' => '+(33)7 01 23 45 67',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn-block btn-outline-danger'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
