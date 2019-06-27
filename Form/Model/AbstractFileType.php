<?php

namespace Puzzle\MediaBundle\Form\Model;

use Puzzle\MediaBundle\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('displayName', TextType::class)
                ->add('caption', TextareaType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => File::class,
            'validation_groups' => array(
                File::class,
                'determineValidationGroups',
            ),
        ));
    }
}