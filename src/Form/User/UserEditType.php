<?php

namespace App\Form\User;

use App\Entity\User;
use App\Enum\Shared\StatusEnum;
use App\Enum\UserRoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => 'Аватар',
//                'mapped' => false, // если поле avatar не мапится напрямую в сущность
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('name', TextType::class, [
                'label' => 'Имя',
            ])
            ->add('email', TextType::class, [
                'label' => 'Почта',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
            ])
            ->add('telegram', TextType::class, [
                'label' => 'Телеграм',
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Роль',
                'choices' => UserRoleEnum::getChoices(),
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Статус',
                'choices' => StatusEnum::cases(),
                'choice_label' => fn(StatusEnum $enum) => match($enum) {
                    StatusEnum::ACTIVE => 'Активный',
                    StatusEnum::INACTIVE => 'Неактивный',
                    StatusEnum::DELETED => 'Удалённый',
                    StatusEnum::BLOCKED => 'Заблокированный',
                    StatusEnum::ARCHIVED => 'Архивный',
                },
                'choice_value' => fn(?StatusEnum $enum) => $enum?->value,
                'required' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}