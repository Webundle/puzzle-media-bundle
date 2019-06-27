<?php

namespace Puzzle\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\MediaBundle\Form\Model\AbstractVideoType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class VideoUpdateType extends AbstractVideoType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'video_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'puzzle_admin_media_video_update';
    }
}