<?php

namespace App\Form\Feedback;

use App\Entity\Feedback\Feedback;
use App\Enum\Feedback\FeedbackScopeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('scope', ChoiceType::class, [
                'label' => 'Область опроса',
                'choices' => FeedbackScopeEnum::cases(),
                'choice_label' => fn(FeedbackScopeEnum $scope) => FeedbackScopeEnum::getChoices()[$scope->value],
                'choice_value' => fn(?FeedbackScopeEnum $scope) => $scope?->value,
                'placeholder' => 'Выберите область',
                'required' => true,
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => FeedbackFieldType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__field__',
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}