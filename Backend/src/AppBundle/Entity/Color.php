<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Color
 *
 * @ORM\Table(name="colors_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ColorRepository")
 */
class Color
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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 6,
     *      max = 6,
     * )
     * @ORM\Column(name="code", type="string", length=255))
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


    /**
     * @ORM\ManyToMany(targetEntity="Wallpaper"  ,mappedBy="colors")
     * @ORM\OrderBy({"created" = "desc"})
     */
    private $wallpapers;



    public function __construct()
    {
        $this->wallpapers = new ArrayCollection();
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
     * @return Color
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
     * Set position
     *
     * @param integer $position
     * @return Color
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
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
    public function getColor()
    {
        return $this;
    }

    /**
    * Get code
    * @return  
    */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
    * Set code
    * @return $this
    */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
