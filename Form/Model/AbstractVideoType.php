<?php

namespace Puzzle\MediaBundle\Form\Model;

use Puzzle\MediaBundle\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractVideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('duration', TextType::class, ['required' => false])
                ->add('country', TextType::class, ['required' => false])
                ->add('actors', TextType::class, ['required' => false])
                ->add('authors', TextType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Video::class,
            'validation_groups' => array(
                Video::class,
                'determineValidationGroups',
            ),
        ));
    }
}