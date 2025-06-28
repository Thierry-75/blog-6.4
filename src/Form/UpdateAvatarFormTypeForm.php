<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Avatar;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Sequentially;

class UpdateAvatarFormTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class,['attr'=>['class'=>'input-gray','placeholder'=>'Votre pseudo','required'=>true,'autofocus'=>true, 'accept'=>'image/png'],
            'label'=>'Pseudonyme',
            'label_attr'=>['class'=>'mb-3 block text-sm text-cyan-600 font-medium '],
            'constraints'=>[
                new Sequentially([
                    new NotBlank(message:'13'),
                    new Regex(
                        pattern:'/^.{6,10}#[0-9]{2}$/i',
                        htmlPattern:'^.{6,10}#[0-9]{2}$')
                    ]),
                ],
            'mapped'=>false
            ])  
            ->add('image',FileType::class,['label'=>false,'multiple'=>false,'mapped'=>false,
            'constraints'=>[
                new Sequentially([
                    new Image(
                        minWidth:'100',
                        maxWidth:'300',
                        minHeight:'75',
                        maxHeight:'225'
                    ),
                    new File(
                        maxSize:'1M',
                        maxSizeMessage:'Max 1024 Mo',
                        extensions:['png'],
                        extensionsMessage:'Image de type png'
                    )
                    
                ])
            ]
            ])

            ->addEventListener(FormEvents::POST_SUBMIT, $this->addDate(...))
            
        ;
    }

    public function addDate(PostSubmitEvent $event)
    {
        $data = $event->getData();
        if (!($data instanceof Avatar)) return;
        $data->setCreatedAt(new \DateTimeImmutable());
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avatar::class,
        ]);
    }
}
