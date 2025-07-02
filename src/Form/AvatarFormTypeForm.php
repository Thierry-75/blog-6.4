<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Sequentially;

class AvatarFormTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                        maxSize:'2M',
                        maxSizeMessage:'Max 2 Mo',
                        extensions:['jpeg'],
                        extensionsMessage:'Image de type jpeg'
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
