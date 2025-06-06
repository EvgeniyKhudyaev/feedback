<?php

namespace App\Form\Feedback;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название опроса',
                'attr' => [
                    'placeholder' => 'Введите название опроса',
                    'required' => true
                ],
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => FeedbackFieldType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__field__',
                'entry_options' => [
                    'label' => false, // Убираем label
                ],
                'label' => false,
            ]);
    }
}