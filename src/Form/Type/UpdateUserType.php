<?php

namespace App\Form\Type;

use App\DTO\UserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Имя студента',
                'attr' => [
                    'placeholder' => 'Имя',
                ],
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия студента',
                'attr' => [
                    'placeholder' => 'Фамилия',
                ],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'email',
                ],
            ])
            ->add('login', TextType::class, [
                'label' => 'Login',
                'attr' => [
                    'placeholder' => 'login',
                ],
            ])
//            ->add('password', PasswordType::class, [
//                'label' => 'Password',
//                'attr' => [
//                    'placeholder' => 'Password'
//                ],
//            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить',
            ])
            ->setMethod('PATCH');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDTO::class,
            'empty_data' => new UserDTO(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'update_user';
    }
}