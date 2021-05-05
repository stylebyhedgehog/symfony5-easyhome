<?php

namespace App\Form;

use App\Entity\Ad;
use App\Service\constants\AdStreetType;
use App\Service\constants\AdTypeRent;
use App\Controller\RegionCityController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rcService=new RegionCityController();


        $builder
            ->add('region',ChoiceType::class,
            [   'attr'=>['class'=>'shadow-none'],
                'choices'=>$rcService->getAllRegions(),
                'choice_label' => function ($choice, $key, $value) {
                        return $value;
                    }
            ])
            ->add('city', ChoiceType::class,[
                'data'=>$builder->getData()->getCity(),

            ])

            ->add('district', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'district  cannot be blank!']),
                    new Length(['min'=>2,
                        'minMessage'=>'ЭЭЭЭЭЭЭЭЭЭЭ'])
                ]
            ])
            ->add('street_type', ChoiceType::class,[
                'choices'=>AdStreetType::street_type(),
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                }
            ])
            ->add('street', TextType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'address  cannot be blank!']),
                ]
            ])
            ->add('house_number',TextType::class)
            ->add('flat_number', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'flat  cannot be blank!']),
                ]
            ])
            ->add('sqr')
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'The description cannot be blank!']),
                ]
            ])
            ->add('type_rent',ChoiceType::class,
            [
                'choices'=>AdTypeRent::typeRent(),
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                }

            ])
            ->add('price', IntegerType::class,[
                'constraints' => [
                    new NotBlank(['message' => 'price  cannot be blank!']),
                ]
            ])
            ->add('images', FileType::class, [
                'multiple' => true,
                'mapped' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class
        ]);
    }
}
