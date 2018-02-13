<?php

namespace App\Form;

use App\Entity\GenerateCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ])
            ->add('font', ChoiceType::class, [
                'choices' => [
                    GenerateCriteria::FONT_GOTHIC => GenerateCriteria::FONT_GOTHIC,
                    GenerateCriteria::FONT_MINCHO => GenerateCriteria::FONT_MINCHO,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'inline' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GenerateCriteria::class,
        ]);
    }
}
