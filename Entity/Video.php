<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Video
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_video")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Video
{   
    use Timestampable;
    
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="duration", type="string", nullable=true)
     */
    private $duration;
    
    /**
     * @var string
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;
    
    /**
     * @var string
     * @ORM\Column(name="authors", type="string", nullable=true)
     */
    private $authors;
    
    /**
     * @var string
     * @ORM\Column(name="actors", type="string", nullable=true)
     */
    private $actors;
    
    /**
     * @ORM\OneToOne(targetEntity="File", fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    public function getId() :? int {
        return $this->id;
    }
    
    public function setDuration(string $duration) :self {
        $this->duration = $duration;
        return $this;
    }
    
    public function getDuration() :?string {
        return $this->duration;
    }
    
    public function setCountry(string $country) :self {
        $this->country = $country;
        return $this;
    }
    
    public function getCountry() :?string {
        return $this->country;
    }
    
    public function setAuthors($authors) :self {
        $this->authors = $authors;
        return $this;
    }
    
    public function getAuthors() :?string {
        return $this->authors;
    }
    
    public function setActors($actors) :self {
        $this->actors = $actors;
        return $this;
    }
    
    public function getActors() :?string {
        return $this->actors;
    }
    
    public function setFile(File $file = null) :self {
        $this->file = $file;
        return $this;
    }

    public function getFile() :?File {
        return $this->file;
    }
}
