<?php
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

$form = $app['form.factory']->createBuilder(FormType::class)
    ->add('moji', TextType::class, [
        'attr' => [
            'autofocus' => true,
            'placeholder' => 'なるほど',
        ],
        'constraints' => [
            new Assert\NotBlank(),
        ],
    ])
    ->getForm()
;

return $form;
