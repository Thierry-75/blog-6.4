<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input-gray',
                    'placeholder' => 'Adresse courriel',
                    'autofocus'=>true,
                    'required' => true
                ],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: ""),
                        new Length(['max' => 180, 'maxMessage' => '']),
                        new Email(message: 'L\'adresse courriel {{ value }} est incorrecte.',)
                    ])
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'input-gray',
                    'placeholder' => 'Pseudo',
                    'autofocus' => false,
                    'required' => true
                ],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(message: ""),
                        new Regex(
                            pattern:'/^.{6,10}#[0-9]{2}$/i',
                            htmlPattern:'^.{6,10}#[0-9]{2}$')
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'input-gray
                ',
                    'placeholder' => 'Mot de passe',
                    'required' => true
                ],
                'constraints' => [
                    new Sequentially([
                        new NotBlank(['message' => '']),
                        new Regex(
                            pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{10}$/i',
                            htmlPattern: '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{10}$'
                        )
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'attr' => [
                    'class' => 'w-4 h-4 border border-gray-50 shadow-inner rounded-lg bg-gray-100 focus:ring-3 focus:ring-primary-300',
                    'required'=>true
                ],
                'label' => ' Accepter les conditions générales',
                'label_attr' => ['class' => 'font-light text-gray-500 ml-4 dark:text-gray-300 text-xs', 'id' => 'agree_state'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => '',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->addData(...))
        ;
    }

    public function addData(PostSubmitEvent $event)
    {
        $data = $event->getData();
        if (!($data instanceof User)) return;
        $data->setCreatedAt(new \DateTimeImmutable());
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
