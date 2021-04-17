<?php


namespace App\Form;


use App\Data\AdData;
use App\Service\constants\AdFilter;
use App\Service\constants\AdStatus;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class AdFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //TODO РЕШИТЬ ОШИБКУ С ОШИБКАМИ ПРИ ВЫХОДЕ ЗА STEP
        $builder
            ->add('q', SearchType::class,
                ['attr' => [
                    'class' => 'form-control shadow-none',
                    'aria-describedby' => 'basic-addon2',
                    'aria-label' => 'Recipients username'
                ],
                    'required' => false]);
            if ($options['mode'] == "agent"){
                $builder
                    ->add('sort_param', ChoiceType::class, [
                        'attr' => [
                            'class' => 'btn btn-secondary dropdown-toggle shadow-none '
                        ],
                        'choices' => AdFilter::$sort_date,
                        'data' => null,

                    ]);
            }
            else{
                $builder
                    ->add('sort_param', ChoiceType::class, [
                        'attr' => [
                            'class' => 'btn btn-secondary dropdown-toggle shadow-none '
                        ],
                        'choices' => AdFilter::$sort,

                    ]);
            }
            $builder
            ->add('min_price', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control shadow-none',
                    'step' => 1000,
                    'min' => 0,
                    'placeholder' => 'От',
                ],
                'label' => false,
                'required' => false,
                'data' => null,
            ])
            ->add('max_price', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control shadow-none',
                    'step' => 1000,
                    'min' => 0,
                    'placeholder' => 'До'
                ],
                'label' => false,
                'required' => false,
            ])
            //RangeType или видос
            ->add('min_sqr', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control shadow-none',
                    'step' => 1,
                    'min' => 0,
                    'placeholder' => 'От'
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('max_sqr', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control shadow-none',
                    'step' => 1,
                    'min' => 0,
                    'placeholder' => 'До'
                ],
                'label' => false,
                'required' => false

            ])
            ->add('search', SubmitType::class,
                ['attr' => ['class' => 'btn btn-primary form-control '],
                    'label' => 'Поиск'
                ]
            )
            ->add('choice_status', ChoiceType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-secondary dropdown-toggle shadow-none '
                    ],
                    'choices' => AdFilter::$status
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdData::class,
            'mode' => 'client'
        ]);
    }
}