<?php


namespace App\Form;


use App\Entity\ApplicationDTO;
use App\Service\constants\AdFilter;
use App\Service\constants\ApplicationFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort_param', ChoiceType::class, [
                'attr' => [
                    'class' => 'btn btn-secondary dropdown-toggle shadow-none '
                ],
                'choices' => ApplicationFilter::$sort,

            ])
            ->add('choice_status', ChoiceType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-secondary dropdown-toggle shadow-none '
                    ],
                    'choices' => ApplicationFilter::$status
                ])
            ->add('search', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ApplicationDTO::class,
        ]);
    }
}