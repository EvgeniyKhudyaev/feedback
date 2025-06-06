<?php

namespace App\Form\Feedback;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedbackFieldOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Вариант',
                'attr' => ['class' => 'form-control'],
            ]);
//            ->add('value', TextType::class, [
//                'label' => 'Значение',
//                'attr' => ['class' => 'form-control'],
//            ]);
    }
}