<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaBundle\Entity\Media;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Wallpaper
 *
 * @ORM\Table(name="wallpapers_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WallpaperRepository")
 */
class Wallpaper
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
     *      min = 3
     * )
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;


    /**
     * @var string
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;


    /**
     * @var string
     * @ORM\Column(name="size", type="string", length=255)
     */
    private $size;


    /**
     * @var string
     * @ORM\Column(name="resolution", type="string", length=255)
     */
    private $resolution;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;



    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

     /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

        /**
     * @var bool
     *
     * @ORM\Column(name="review", type="boolean")
     */
    private $review;
    /**
     * @var bool
     *
     * @ORM\Column(name="premium", type="boolean")
     */
    private $premium;

        /**
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="wallpaper_categories_table",
     *      joinColumns={@ORM\JoinColumn(name="wallpaper_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id",onDelete="CASCADE")},
     *      )
     */
    private $categories;
     /**
     * @ORM\ManyToMany(targetEntity="Color")
     * @ORM\JoinTable(name="wallpaper_colors_table",
     *      joinColumns={@ORM\JoinColumn(name="wallpaper_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="color_id", referencedColumnName="id",onDelete="CASCADE")},
     *      )
     */
    private $colors;
    /**
     * @ORM\ManyToMany(targetEntity="Pack")
     * @ORM\JoinTable(name="wallpaper_packs_table",
     *      joinColumns={@ORM\JoinColumn(name="wallpaper_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="pack_id", referencedColumnName="id",onDelete="CASCADE")},
     *      )
     */
    private $packs;
    /**
    * @ORM\OneToMany(targetEntity="Rate", mappedBy="wallpaper",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $rates;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="wallpapers_tags_table",
     *      joinColumns={@ORM\JoinColumn(name="wallpaper_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id",onDelete="CASCADE")},
     *      )
     */
    private $tagslist;


    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="200M")
     */
    private $file;

    /**
     * @Assert\File(mimeTypes={"image/gif" },maxSize="200M")
     */
    private $filegif;

    /**
     * @Assert\File(mimeTypes={"video/mp4","video/x-m4v" },maxSize="200M")
     */
    private $filevideo;
    /**
     * @Assert\Url()
     * @Assert\Length(
     *      min = 3,
     * )
     */
    private $urlvideo;

    /**
    * @ORM\OneToMany(targetEntity="Comment", mappedBy="wallpaper",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $comments;



    /**
     * @var bool
     *
     * @ORM\Column(name="comment", type="boolean")
     */
    private $comment;


    /**
     * @var string
     * @ORM\Column(name="tags", type="string", length=255,nullable=true)
     */
    private $tags;


        /**
     * @var int
     *
     * @ORM\Column(name="shares", type="integer")
     */
    private $shares;


        /**
     * @var int
     *
     * @ORM\Column(name="sets", type="integer")
     */
    private $sets;

    /**
     * @var int
     *
     * @ORM\Column(name="downloads", type="integer")
     */
    private $downloads;

    /**
     * @var int
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;


    public function __construct()
    {
        $this->downloads = 0 ;
        $this->shares = 0 ;
        $this->sets = 0 ;
        $this->views = 0 ;
        $this->categories = new ArrayCollection();
        $this->colors = new ArrayCollection();
        $this->packs = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->created= new \DateTime();
        $this->review = false;
    }

    

   
   /**
    * Get id
    * @return  
    */
    public function getId()
    {
        return $this->id;
    }
    
    /**
    * Set id
    * @return $this
    */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Wallpaper
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
     * Set downloads
     *
     * @param integer $downloads
     * @return Wallpaper
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Wallpaper
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

   
    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Album
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
   
    /**
     * Set user
     *
     * @param string $user
     * @return Wallpaper
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set media
     *
     * @param string $media
     * @return Wallpaper
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

   
     /**
     * Add categories
     *
     * @param Wallpaper $categories
     * @return Categorie
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param Category $categories
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
     /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setCategories($categories)
    {
        return $this->categories =  $categories;
    }


    /**
     * Add color
     *
     * @param Wallpaper $colors
     * @return Colors
     */
    public function addColor(Color $color)
    {
        $this->colors[] = $color;

        return $this;
    }

    /**
     * Remove color
     *
     * @param Color $colors
     */
    public function removeColor(Color $color)
    {
        $this->colors->removeElement($color);
    }

    /**
     * Get colors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getColors()
    {
        return $this->colors;
    }
        /**
     * Get colors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setColors($colors)
    {
        return $this->colors =  $colors;
    }

    /**
     * Add pack
     *
     * @param Wallpaper $packs
     * @return Packs
     */
    public function addPack(Pack $pack)
    {
        $this->packs[] = $pack;

        return $this;
    }

    /**
     * Remove pack
     *
     * @param Pack $packs
     */
    public function removePack(Pack $pack)
    {
        $this->packs->removeElement($pack);
    }

    /**
     * Get packs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPacks()
    {
        return $this->packs;
    }
        /**
     * Get packs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setPacks($packs)
    {
        return $this->packs =  $packs;
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
    * Get filevideo
    * @return  
    */
    public function getFilevideo()
    {
        return $this->filevideo;
    }
    
    /**
    * Set filevideo
    * @return $this
    */
    public function setFilevideo($filevideo)
    {
        $this->filevideo = $filevideo;
        return $this;
    }
    
    /**
    * Get review
    * @return  
    */
    public function getReview()
    {
        return $this->review;
    }
    
    /**
    * Set review
    * @return $this
    */
    public function setReview($review)
    {
        $this->review = $review;
        return $this;
    }
    public function __toString()
    {
        return $this->title;
    }
        /**
    * Get comment
    * @return  
    */
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
    * Set comment
    * @return $this
    */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

       /**
     * Add comments
     *
     * @param Wallpaper $comments
     * @return Categorie
     */
    public function addComment(Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param Comment $comments
     */
    public function removeComment(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
    /**
    * Get tags
    * @return  
    */
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
    * Set tags
    * @return $this
    */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }
    /**
    * Get shares
    * @return  
    */
    public function getShares()
    {
        return $this->shares;
    }
    
    /**
    * Set shares
    * @return $this
    */
    public function setShares($shares)
    {
        $this->shares = $shares;
        return $this;
    }
    /**
    * Get love
    * @return  
    */
    public function getLove()
    {
        return $this->love;
    }
    
    /**
    * Set love
    * @return $this
    */
    public function setLove($love)
    {
        $this->love = $love;
        return $this;
    }
    /**
    * Get angry
    * @return  
    */
    public function getAngry()
    {
        return $this->angry;
    }
    
    /**
    * Set angry
    * @return $this
    */
    public function setAngry($angry)
    {
        $this->angry = $angry;
        return $this;
    }
    /**
    * Get woow
    * @return  
    */
    public function getWoow()
    {
        return $this->woow;
    }
    
    /**
    * Set woow
    * @return $this
    */
    public function setWoow($woow)
    {
        $this->woow = $woow;
        return $this;
    }
    /**
    * Get haha
    * @return  
    */
    public function getHaha()
    {
        return $this->haha;
    }
    
    /**
    * Set haha
    * @return $this
    */
    public function setHaha($haha)
    {
        $this->haha = $haha;
        return $this;
    }
    /**
    * Get sad
    * @return  
    */
    public function getSad()
    {
        return $this->sad;
    }
    
    /**
    * Set sad
    * @return $this
    */
    public function setSad($sad)
    {
        $this->sad = $sad;
        return $this;
    }
    /**
    * Get video
    * @return  
    */
    public function getVideo()
    {
        return $this->video;
    }
    
    /**
    * Set video
    * @return $this
    */
    public function setVideo(Media $video)
    {
        $this->video = $video;
        return $this;
    }
    /**
    * Get urlvideo
    * @return  
    */
    public function getUrlvideo()
    {
        return $this->urlvideo;
    }
    
    /**
    * Set urlvideo
    * @return $this
    */
    public function setUrlvideo($urlvideo)
    {
        $this->urlvideo = $urlvideo;
        return $this;
    }
    /**
    * Get description
    * @return  
    */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
    * Set description
    * @return $this
    */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    /**
    * Get type
    * @return  
    */
    public function getType()
    {
        return $this->type;
    }
    
    /**
    * Set type
    * @return $this
    */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
    * Get color
    * @return  
    */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
    * Set color
    * @return $this
    */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
    public function getClear()
    {
        return base64_decode($this->title);
    }
    /**
    * Get views
    * @return  
    */
    public function getViews()
    {
        return $this->views;
    }
     /**
    * Get views
    * @return  
    */
    public function getViewsnumber()
    {
        return $this->number_format_short($this->views). " View(s)";
    }  
     /**
    * Get views
    * @return  
    */
    public function getDownloadsnumber()
    {
        return $this->number_format_short($this->downloads) . " Download(s)";
    }   
     /**
    * Get views
    * @return  
    */
    public function getSetsnumber()
    {
        return $this->number_format_short($this->sets) . " Set(s)";
    }   
     /**
    * Get views
    * @return  
    */
    public function getSharesnumber()
    {
        return $this->number_format_short($this->shares) . " Share(s)";
    }   
    /**
    * Set views
    * @return $this
    */
    public function setViews($views)
    {
        $this->views = $views;
        return $this;
    }
    /**
     * @param $n
     * @return string
     * Use to convert large positive numbers in to short form shares 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
     */
    function number_format_short( $n ) {
        if ($n==0){
             return 0;
        }
        if ($n > 0 && $n < 1000) {
            // 1 - 999
            $n_format = floor($n);
            $suffix = '';
        } else if ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $n_format = floor($n / 1000);
            $suffix = 'K+';
        } else if ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $n_format = floor($n / 1000000);
            $suffix = 'M+';
        } else if ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $n_format = floor($n / 1000000000);
            $suffix = 'B+';
        } else if ($n >= 1000000000000) {
            // 1t+
            $n_format = floor($n / 1000000000000);
            $suffix = 'T+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
    }
    /**
    * Get size
    * @return  
    */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
    * Set size
    * @return $this
    */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
    /**
    * Get premium
    * @return  
    */
    public function getPremium()
    {
        return $this->premium;
    }
    
    /**
    * Set premium
    * @return $this
    */
    public function setPremium($premium)
    {
        $this->premium = $premium;
        return $this;
    }

    /** Get rating
    * @return  
    */
    public function getRating()
    {
        return $this->rating;
    }
    
    /**
    * Set rating
    * @return $this
    */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }
          /**
     * Add tags
     *
     * @param Wallpaper $tags
     * @return tag
     */
    public function addTagslist(Tag $tagslist)
    {
        $this->tagslist[] = $tagslist;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param tag $tags
     */
    public function removeTagslist(Tag $tagslist)
    {
        $this->tagslist->removeElement($tagslist);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagslist()
    {
        return $this->tagslist;
    }
        /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setTagslist($tagslist)
    {
        return $this->tagslist =  $tagslist;
    }
    /**
    * Get resolution
    * @return  
    */
    public function getResolution()
    {
        return $this->resolution;
    }
    
    /**
    * Set resolution
    * @return $this
    */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
        return $this;
    }
    /**
    * Get sets
    * @return  
    */
    public function getSets()
    {
        return $this->sets;
    }
    
    /**
    * Set sets
    * @return $this
    */
    public function setSets($sets)
    {
        $this->sets = $sets;
        return $this;
    }
    /**
    * Get filegif
    * @return  
    */
    public function getFilegif()
    {
        return $this->filegif;
    }
    
    /**
    * Set filegif
    * @return $this
    */
    public function setFilegif($filegif)
    {
        $this->filegif = $filegif;
        return $this;
    }
}
