<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Category
 *
 * @ORM\Table(name="categories_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


    /**
     * @ORM\ManyToMany(targetEntity="Wallpaper"  ,mappedBy="categories")
     * @ORM\OrderBy({"created" = "desc"})
     */
    private $wallpapers;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $file;
     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;



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
     * @return Category
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
     * @return Category
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
    public function getCategory()
    {
        return $this;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    /**
     * Set media
     *
     * @param string $media
     * @return image
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string 
     */
    public function getMedia()
    {
        return $this->media;
    }
}
