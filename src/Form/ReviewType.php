<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'label' => "Комментарий"
                ])
            ->add('rating')
            ->add('save', SubmitType::class,
            [
                'attr'=>['class'=>'btn btn-primary'],
                'label'=>'Отправить'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
