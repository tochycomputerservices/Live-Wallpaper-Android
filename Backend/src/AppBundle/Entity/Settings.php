<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
     *
     * @ORM\Column(name="firebasekey", type="text")
     */
    private $firebasekey;

    /**
     * @var string
     *
     * @ORM\Column(name="publisherid", type="string", length=255 , nullable = true)
     */
    private $publisherid;


    /**
     * @var string
     *
     * @ORM\Column(name="appid", type="string", length=255 , nullable = true)
     */
    private $appid;

    /**
     * @var string
     *
     * @ORM\Column(name="rewardedadmobid", type="string", length=255 , nullable = true)
     */
    private $rewardedadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="banneradmobid", type="string", length=255 , nullable = true)
     */
    private $banneradmobid;


    /**
     * @var string
     *
     * @ORM\Column(name="bannerfacebookid", type="string", length=255 , nullable = true)
     */
    private $bannerfacebookid;



    /**
     * @var string
     *
     * @ORM\Column(name="bannertype", type="string", length=255 , nullable = true)
     */
    private $bannertype;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeadmobid", type="string", length=255 , nullable = true)
     */
    private $nativeadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativefacebookid", type="string", length=255 , nullable = true)
     */
    private $nativefacebookid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeitem",  type="integer",  length=255 , nullable = true)
     */
    private $nativeitem;


    /**
     * @var string
     *
     * @ORM\Column(name="nativetype", type="string", length=255 , nullable = true)
     */
    private $nativetype;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialadmobid", type="string", length=255 , nullable = true)
     */
    private $interstitialadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialfacebookid", type="string", length=255 , nullable = true)
     */
    private $interstitialfacebookid;


     /**
     * @var string
     *
     * @ORM\Column(name="interstitialtype", type="string", length=255 , nullable = true)
     */
    private $interstitialtype;

     /**
     * @var string
     *
     * @ORM\Column(name="interstitialclick", type="integer", length=255 , nullable = true)
     */
    private $interstitialclick;
   
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
    * Get firebasekey
    * @return  
    */
    public function getFirebasekey()
    {
        return $this->firebasekey;
    }
    
    /**
    * Set firebasekey
    * @return $this
    */
    public function setFirebasekey($firebasekey)
    {
        $this->firebasekey = $firebasekey;
        return $this;
    }
       /**
    * Get banneradmobid
    * @return  
    */
    public function getBanneradmobid()
    {
        return $this->banneradmobid;
    }
    
    /**
    * Set banneradmobid
    * @return $this
    */
    public function setBanneradmobid($banneradmobid)
    {
        $this->banneradmobid = $banneradmobid;
        return $this;
    }

    /**
    * Get bannerfacebookid
    * @return  
    */
    public function getBannerfacebookid()
    {
        return $this->bannerfacebookid;
    }
    
    /**
    * Set bannerfacebookid
    * @return $this
    */
    public function setBannerfacebookid($bannerfacebookid)
    {
        $this->bannerfacebookid = $bannerfacebookid;
        return $this;
    }

    /**
    * Get nativefacebookid
    * @return  
    */
    public function getNativefacebookid()
    {
        return $this->nativefacebookid;
    }
    
    /**
    * Set nativefacebookid
    * @return $this
    */
    public function setNativefacebookid($nativefacebookid)
    {
        $this->nativefacebookid = $nativefacebookid;
        return $this;
    }

    /**
    * Get nativeadmobid
    * @return  
    */
    public function getNativeadmobid()
    {
        return $this->nativeadmobid;
    }
    
    /**
    * Set nativeadmobid
    * @return $this
    */
    public function setNativeadmobid($nativeadmobid)
    {
        $this->nativeadmobid = $nativeadmobid;
        return $this;
    }

    /**
    * Get interstitialfacebookid
    * @return  
    */
    public function getInterstitialfacebookid()
    {
        return $this->interstitialfacebookid;
    }
    
    /**
    * Set interstitialfacebookid
    * @return $this
    */
    public function setInterstitialfacebookid($interstitialfacebookid)
    {
        $this->interstitialfacebookid = $interstitialfacebookid;
        return $this;
    }

    /**
    * Get interstitialadmobid
    * @return  
    */
    public function getInterstitialadmobid()
    {
        return $this->interstitialadmobid;
    }
    
    /**
    * Set interstitialadmobid
    * @return $this
    */
    public function setInterstitialadmobid($interstitialadmobid)
    {
        $this->interstitialadmobid = $interstitialadmobid;
        return $this;
    }

    /**
    * Get bannertype
    * @return  
    */
    public function getBannertype()
    {
        return $this->bannertype;
    }
    
    /**
    * Set bannertype
    * @return $this
    */
    public function setBannertype($bannertype)
    {
        $this->bannertype = $bannertype;
        return $this;
    }

    /**
    * Get interstitialtype
    * @return  
    */
    public function getInterstitialtype()
    {
        return $this->interstitialtype;
    }
    
    /**
    * Set interstitialtype
    * @return $this
    */
    public function setInterstitialtype($interstitialtype)
    {
        $this->interstitialtype = $interstitialtype;
        return $this;
    }

    /**
    * Get nativetype
    * @return  
    */
    public function getNativetype()
    {
        return $this->nativetype;
    }
    
    /**
    * Set nativetype
    * @return $this
    */
    public function setNativetype($nativetype)
    {
        $this->nativetype = $nativetype;
        return $this;
    }

    /**
    * Get interstitialclick
    * @return  
    */
    public function getInterstitialclick()
    {
        return $this->interstitialclick;
    }
    
    /**
    * Set interstitialclick
    * @return $this
    */
    public function setInterstitialclick($interstitialclick)
    {
        $this->interstitialclick = $interstitialclick;
        return $this;
    }

    /**
    * Get nativeitem
    * @return  
    */
    public function getNativeitem()
    {
        return $this->nativeitem;
    }
    
    /**
    * Set nativeitem
    * @return $this
    */
    public function setNativeitem($nativeitem)
    {
        $this->nativeitem = $nativeitem;
        return $this;
    }

    /**
    * Get rewardedadmobid
    * @return  
    */
    public function getRewardedadmobid()
    {
        return $this->rewardedadmobid;
    }
    
    /**
    * Set rewardedadmobid
    * @return $this
    */
    public function setRewardedadmobid($rewardedadmobid)
    {
        $this->rewardedadmobid = $rewardedadmobid;
        return $this;
    }


    /**
    * Get publisherid
    * @return  
    */
    public function getPublisherid()
    {
        return $this->publisherid;
    }
    
    /**
    * Set publisherid
    * @return $this
    */
    public function setPublisherid($publisherid)
    {
        $this->publisherid = $publisherid;
        return $this;
    }

    /**
    * Get appid
    * @return  
    */
    public function getAppid()
    {
        return $this->appid;
    }
    
    /**
    * Set appid
    * @return $this
    */
    public function setAppid($appid)
    {
        $this->appid = $appid;
        return $this;
    }
}
