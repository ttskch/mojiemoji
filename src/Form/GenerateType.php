<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GenerateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('moji', TextType::class, [
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'なるほど',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('font', ChoiceType::class, [
                'choices' => $fontChoices = [
                    'ゴシック' => 'ゴシック',
                    '明朝' => '明朝',
                ],
                'data' => 'ゴシック',
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'inline' => true,
                ],
                'constraints' => [
                    new Assert\Choice(array_keys($fontChoices)),
                ],
            ])
        ;
    }
}
