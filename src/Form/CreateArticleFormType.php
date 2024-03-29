<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'remplir le champ !'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 150,
                    ])
                ]
            ])
            ->add('content', CKEditorType::class, [
                'purify_html' => true,
                'label' => 'contenu',
                'attr' => [
                    'class' => 'd-none',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'remplir le champ !'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50000,
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'publier',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
