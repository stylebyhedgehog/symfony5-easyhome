<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'city  cannot be blank!']),
                ]
            ])
            ->add('address', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'address  cannot be blank!']),
                ]
            ])
            ->add('flat', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'flat  cannot be blank!']),
                ]
            ])
            ->add('sqr', IntegerType::class)
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'The description cannot be blank!']),
                ]
            ])

            ->add('price', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'price  cannot be blank!']),
                ]
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
            'csrf_protection' => false,
        ]);
    }
}
