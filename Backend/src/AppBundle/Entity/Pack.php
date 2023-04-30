<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Pack
 *
 * @ORM\Table(name="packs_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackRepository")
 */
class Pack
{
   
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 25,
     * )
     * @ORM\Column(name="title", type="string", length=255))
     */
    private $title;

   /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;


    /**
     * @ORM\ManyToMany(targetEntity="Wallpaper"  ,mappedBy="packs")
     * @ORM\OrderBy({"created" = "desc"})
     */
    private $wallpapers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    public function __construct()
    {
        $this->wallpapers = new ArrayCollection();
        $this->created= new \DateTime();

    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Pack
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Add image
     *
     * @param image $image
     * @return Categorie
     */
    public function addWallpaper(Wallpaper $element)
    {
        $this->wallpapers[] = $element;

        return $this;
    }

    /**
     * Remove wallpapers
     *
     * @param wallpapers $wallpapers
     */
    public function removeWallpaper(Wallpaper $element)
    {
        $this->wallpapers->removeElement($element);
    }

    /**
     * Get wallpapers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWallpapers()
    {
        return $this->wallpapers;
    }
        public function __toString()
    {
        return $this->title;
    }
    public function getPack()
    {
        return $this;
    }

    /**
    * Get enabled
    * @return  
    */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    /**
    * Set enabled
    * @return $this
    */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
    /**
    * Get created
    * @return  
    */
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
    * Set created
    * @return $this
    */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }
}
