<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'input-gray'],
                'label' => 'Titre de l\'article :',
                'label_attr' => ['class' => 'block text-lg font-medium text-gray-800 mb-1']
            ])
            ->add('content', TinymceType::class, [
                'attr' => [
                    'class' => 'input-gray',
                    'required' => true,
                    'toolbar' => 'undo redo | bold italic | forecolor backcolor | template | alignleft aligncenter alignright alignjustify | bullist numlist | link | spellchecker',
                    'id' => 'article_form_content',
                    'height' => 390
                ],
                'label' => 'Corps de l\'article :',
                'label_attr' => ['class' => 'block text-lg font-medium text-gray-800 mb-1']
            ])
            ->add('photo', FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'label'=>'Photo :',
                'label_attr' => ['class' => 'block text-lg font-medium text-gray-800 mb-1'],
                'constraints' => [
                    new Sequentially([
                        new Image(
                            minWidth: '1280',
                            maxWidth: '1920',
                            minHeight: '720',
                            maxHeight: '1080'
                        ),
                        new File(
                            maxSize: '2M',
                            maxSizeMessage: 'Max 2Mo',
                            extensions: ['jpeg','jpg'],
                            extensionsMessage: 'Image de type jpeg'
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
        if (!($data instanceof Article)) return;
        $data->setCreatedAt(new \DateTimeImmutable());
        $data->setSlug(strtolower($data->getTitle()));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
