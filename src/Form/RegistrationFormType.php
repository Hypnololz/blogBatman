<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un pseudo !'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 40,
                        'minMessage' => 'votre pseudo doit faire au minimum {{ limit }} caractère',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new Email([
                        'message' => "L'adresse Email {{ value }} n'est pas une adresse valide"
                    ]),
                    new NotBlank([
                        'message' => 'Merci de renseigner une adresse email',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'les mots de passes doivent etre identique',
                'first_options'=>[
                  'label' => 'mot de passe'
                ],
                'second_options'=>[
                  'label' => 'confirmation du mot de passe'
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe !',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'votre mot de passe doit contenir au minimum{{ limit }} caractère',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/",
                        'message' => 'votre mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et caractère spécial',
                    ]),
                ],
            ])
            ->add('save',SubmitType::class,[
                'label' => 'inscription',
                'attr' => [
                  'class' => 'btn btn-success w-100'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
//            TODO: Penser a viré cette merde
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
