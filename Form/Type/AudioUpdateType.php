<?php

namespace Puzzle\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\MediaBundle\Form\Model\AbstractAudioType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AudioUpdateType extends AbstractAudioType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'audio_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'puzzle_admin_media_audio_update';
    }
}