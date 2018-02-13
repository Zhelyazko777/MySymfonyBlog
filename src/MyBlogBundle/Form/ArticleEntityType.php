<?php

namespace MyBlogBundle\Form;

use function PHPSTORM_META\type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleEntityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('body', TextareaType::class)
            ->add('category', EntityType::class, [

                'placeholder' => 'Choose a category',

                'class' => 'MyBlogBundle:Category',

                'choice_label' => 'name'

            ])
            ->add('tags', EntityType::class, [

                'class' => 'MyBlogBundle\Entity\Tag',

                'multiple' => true,

                'expanded' => true,

                'choice_label' => 'name'
            ])
            ->add('save',SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MyBlogBundle\Entity\ArticleEntity'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'myblogbundle_articleentity';
    }


}
