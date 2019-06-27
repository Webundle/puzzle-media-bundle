<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Audio 
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_audio")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\AudioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Audio
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
     * @ORM\Column(name="author", type="string", nullable=true)
     */
    private $author;
    
    /**
     * @var array
     * @ORM\Column(name="album", type="string", nullable=true)
     */
    private $album;
    
    /**
     * @var string
     * @ORM\Column(name="artists", type="string", nullable=true)
     */
    private $artists;
    
    /**
     * @var string
     * @ORM\Column(name="compositor", type="string", nullable=true)
     */
    private $compositor;
    
    /**
     * @var string
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    private $gender;
    
    /**
     * @var string
     * @ORM\Column(name="year", type="string", nullable=true)
     */
    private $year;
    
    /**
     * @var string
     * @ORM\Column(name="track_number", type="integer", nullable=true)
     */
    private $trackNumber;
    
    /**
     * @var string
     * @ORM\Column(name="track_total", type="integer", nullable=true)
     */
    private $trackTotal;
    
    /**
     * @ORM\OneToOne(targetEntity="File", fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    public function getId() :? int {
        return $this->id;
    }
    
    public function setAuthor(string $author) :self {
        $this->author = $author;
        return $this;
    }
    
    public function getAuthor() :?string {
        return $this->author;
    }
    
    public function setAlbum(string $album) :self {
        $this->album = $album;
        return $this;
    }
    
    public function getAlbum() :?string {
        return $this->album;
    }
    
    public function setArtists(string $artists) :self {
        $this->artists = $artists;
        return $this;
    }
    
    public function getArtists() :?string {
        return $this->artists;
    }
    
    public function setCompositor (string $compositor) :self {
        $this->compositor = $compositor;
        return $this;
    }
    
    public function getCompositor() :?string {
        return $this->compositor;
    }
    
    public function setGender(string $gender) :self {
        $this->gender = $gender;
        return $this;
    }
    
    public function getGender() :?string {
        return $this->gender;
    }
    
    public function setYear(string $year) :self {
        $this->year = $year;
        return $this;
    }
    
    public function getYear() :?string {
        return $this->year;
    }
    
    public function setTrackNumber(int $trackNumber) :self {
        $this->trackNumber = $trackNumber;
        return $this;
    }
    
    public function getTrackNumber() :?int {
        return $this->trackNumber;
    }
    
    public function setTrackTotal(int $trackTotal) :self {
        $this->trackTotal = $trackTotal;
        return $this;
    }
    
    public function getTrackTotal() :?int {
        return $this->trackTotal;
    }
    
    public function setFile(File $file = null) : self {
        $this->file = $file;
        return $this;
    }

    public function getFile() :? File {
        return $this->file;
    }
}
