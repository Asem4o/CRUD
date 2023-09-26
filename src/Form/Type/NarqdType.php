<?php

namespace App\Form\Type;

// src/Form/Type/OrderType.php

use App\Entity\Narqd;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NarqdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ...
            ->add('compensationHours', NumberType::class)
            ->add('createdAt', DateType::class,
                ['widget' => 'single_text',

                    'html5' => false,
                    'attr' => [
                        'class' => 'flatpickr form-select',
                        'placeholder' => 'yyyy-MM-dd',
                    ],

                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enter Compensation', // Change the text of the submit button here
                'attr' => [
                    'class' => 'btn btn-success', // You can also add custom classes to the button
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Narqd::class,
        ]);
    }
}