<?php

namespace App\Form\Feedback;

use App\Entity\Feedback\FeedbackField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\Feedback\FeedbackFieldTypeEnum as FeedbackFieldTypeEnum;

class FeedbackFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Вопрос',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'placeholder' => 'Введите вопрос',
                    'style' => 'max-width: 400px;',
                ],
                'label_attr' => [
                    'class' => 'form-label fw-semibold mb-2 me-2'
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Тип',
                'required' => true,
                'choices' => FeedbackFieldTypeEnum::cases(),
                'choice_value' => fn(?FeedbackFieldTypeEnum $choice) => $choice?->value,
                'choice_label' => fn($choice) => match($choice) {
                    FeedbackFieldTypeEnum::INPUT => 'Текст',
                    FeedbackFieldTypeEnum::TEXTAREA => 'Многострочный текст',
                    FeedbackFieldTypeEnum::CHECKBOX => 'Чекбокс',
                    FeedbackFieldTypeEnum::SELECT => 'Список',
                    FeedbackFieldTypeEnum::RADIO => 'Радио',
                    FeedbackFieldTypeEnum::MULTISELECT => 'Множественный список',
                    FeedbackFieldTypeEnum::RATING => 'Рейтинг',
                    FeedbackFieldTypeEnum::FILE => 'Файл',
                },
                'placeholder' => 'Выберите тип',
                'attr' => [
                    'class' => 'form-select d-inline-block mb-2 type-select',
                    'style' => 'max-width: 200px;',
                ],
                'label_attr' => [
                    'class' => 'form-label fw-semibold mb-2 me-2'
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'Обязательное поле',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-check-label fw-semibold me-2',
                ],
            ])
            ->add('options', CollectionType::class, [
                'entry_type' => FeedbackFieldOptionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__option__',
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FeedbackField::class,
        ]);
    }
}