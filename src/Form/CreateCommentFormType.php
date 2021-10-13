<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content',TextareaType::class,[
                'label'=> false,
                'attr' => [
                    'rows' =>10,
                    'placeholder' => 'laissez votre commentaire...'
                ],
                'constraints'=> [
                    new NotBlank([
                        'message' => 'remplir le champ svp'
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 2000,
                        'minMessage' => 'Le commentaire doit contenir au moin {{ limit }}',
                        'maxMessage' => 'Le commentaire doit contenir au max {{ limit }}',
                    ])
                ],
            ])
            ->add('save',SubmitType::class,[
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
