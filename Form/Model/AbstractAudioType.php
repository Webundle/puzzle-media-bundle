<?php

namespace Puzzle\MediaBundle\Form\Model;

use Puzzle\MediaBundle\Entity\Audio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractAudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('author', TextType::class, ['required' => false])
                ->add('album', TextType::class, ['required' => false])
                ->add('artists', TextType::class, ['required' => false])
                ->add('compositor', TextType::class, ['required' => false])
                ->add('gender', TextType::class, ['required' => false])
                ->add('year', TextType::class, ['required' => false])
                ->add('trackNumber', TextType::class, ['required' => false])
                ->add('trackTotal', TextType::class, ['required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Audio::class,
            'validation_groups' => array(
                Audio::class,
                'determineValidationGroups',
            ),
        ));
    }
}