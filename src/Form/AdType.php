<?php

namespace App\Form;

use App\Entity\Ad;
use App\Service\constants\AdStreetType;
use App\Service\constants\AdTypeRent;
use App\Controller\RegionCityController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rcService = new RegionCityController();

        $builder
            ->add('region', ChoiceType::class,
                [
                    'choices' => $rcService->getAllRegions(),
                    'choice_label' => function ($choice, $key, $value) {
                        return $value;
                    },
                    'constraints' => [
                        new NotBlank(['message' => 'Выберите регион']),
                    ]
                ])
            ->add('city', ChoiceType::class,
                [
                    'data' => $builder->getData()->getCity(),
                    'constraints' => [
                        new NotBlank(['message' => 'Выберите город']),
                    ]
                ])
            ->add('district', TextType::class)
            ->add('street_type', ChoiceType::class,
                [
                    'choices' => AdStreetType::street_type(),
                    'choice_label' => function ($choice, $key, $value) {
                        return $value;
                    }
                ])
            ->add('street', TextType::class,
                [
                    'constraints' => [
                        new NotBlank(['message' => 'Выберите улицу']),
                    ]
                ])
            ->add('house_number', TextType::class,
                ['constraints' =>
                    [
                        new NotBlank(['message' => 'Выберите номер дома']),
                        new Regex('(\d+[a-z/\d]*)', "Неправильный формат")
                    ]
                ])
            ->add('flat_number', TextType::class,
                [
                    'attr' => ['novalidate' => 'novalidate'],
                ])
            ->add('sqr', TextType::class,
                [
                    'constraints' =>
                        [
                            new NotBlank(['message' => 'Выберите площадь']),
                            new Regex('/^[0-9]*[.]?[0-9]+$/', "Неправильный формат")
                        ]
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(['message' => 'Добавьте описание']),
                    ]
                ])
            ->add('type_rent', ChoiceType::class,
                [
                    'choices' => AdTypeRent::typeRent(),
                    'choice_label' => function ($choice, $key, $value) {
                        return $value;
                    }

                ])
            ->add('price', IntegerType::class,
                [
                    'constraints' => [
                        new NotBlank(['message' => 'Добавьте стоимость']),
                    ]
                ])
            ->add('images', FileType::class,
                [
                    'multiple' => true,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => 'Прикрепите изображение']),
                    ]
                ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class
        ]);
    }
}
