<?php

namespace App\Form\Feedback;

use App\Enum\Feedback\FeedbackScopeEnum;
use App\Enum\Feedback\FeedbackTypeEnum;
use App\Enum\Shared\StatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedbackFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', IntegerType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('type', ChoiceType::class, [
                'choices' => FeedbackTypeEnum::getChoices(),
                'required' => false,
            ])
            ->add('scope', ChoiceType::class, [
                'choices' => FeedbackScopeEnum::getChoices(),
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => StatusEnum::getChoices(),
                'required' => false,
            ]);
    }
}