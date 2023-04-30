<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Device;
use MediaBundle\Entity\Media;
use AppBundle\Form\SettingsType;
use AppBundle\Form\AdsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class HomeController extends Controller
{
    function send_notificationToken ($tokens, $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids'  => $tokens,
            'data'   => $message

            );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
    function send_notification ($tokens, $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to'  => '/topics/UltimateWallpaperAppTopic',
            'data'   => $message
            );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }

    public function api_firstAction(Request $request,$token){
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
       #----------- categories -----------------------#

        $em=$this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $list=array();
        $repository_category = $em->getRepository('AppBundle:Category');
        $query_category = $repository_category->createQueryBuilder('C')
          ->select(array("C.id","C.title","m.url as image","m.extension as extension","SUM(w.downloads) as test"))
          ->leftJoin('C.wallpapers', 'w')
          ->leftJoin('C.media', 'm')
          ->groupBy('C.id')
          ->orderBy('test',"DESC")
          ->where('w.enabled=true')
          ->getQuery();
        $categories = $query_category->getResult();


      #----------- wallpapers -----------------------#

        $nombre = 30;
        $page = 0;
        $em = $this->getDoctrine()->getManager();
        $repository_wallpaper = $em->getRepository('AppBundle:Wallpaper');
        $query_wallpaper = $repository_wallpaper->createQueryBuilder('w')
          ->where("w.enabled = true")
          ->addOrderBy('w.created', 'DESC')
          ->addOrderBy('w.id', 'asc')
          ->setFirstResult($nombre * $page)
          ->setMaxResults($nombre)
          ->getQuery();
        $wallpapers = $query_wallpaper->getResult();
      #----------- slides -----------------------#
    $em = $this->getDoctrine()->getManager();
    $slides = $em->getRepository("AppBundle:Slide")->findBy(array(), array("position" => "asc"));
      #-------------- packs --------------------#
      
        $em=$this->getDoctrine()->getManager();
        $packs=$em->getRepository("AppBundle:Pack")->findBy(array("enabled"=>true),array("created"=>"desc"));
        $packs_list=array();
        foreach ($packs as $key => $pack) {
            if(sizeof($pack->getWallpapers())>0){
                $s["id"]=$pack->getId();
                $s["title"]=$pack->getTitle();
                $images= array();
                foreach ($pack->getWallpapers() as $key => $wall) {
                    $images[]=$imagineCacheManager->getBrowserPath($wall->getMedia()->getLink(), 'category_thumb_api');
                }
                $s["images"] =  $images;
                $packs_list[]=$s;
            }
        }

        return $this->render('AppBundle:Home:api_first.html.php', array("packs"=>$packs_list,"slides"=>$slides,"categories"=>$categories,"wallpapers" => $wallpapers));
    }
    public function notifCategoryAction(Request $request)
    {
        memory_get_peak_usage();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');



        $em=$this->getDoctrine()->getManager();
        $categories= $em->getRepository("AppBundle:Category")->findAll();

        $devices= $em->getRepository('AppBundle:Device')->findAll();
        $tokens=array();
        foreach ($devices as $key => $device) {
           $tokens[]=$device->getToken();
        }

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
           # ->add('url', UrlType::class)
           # ->add('categories', ChoiceType::class, array('choices' => $categories ))           
            ->add('category', 'entity', array('class' => 'AppBundle:Category'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $category_selected = $em->getRepository("AppBundle:Category")->find($data["category"]);

            $message = array(
                        "type"=>"category",
                        "id"=>$category_selected->getId(),
                        "title_category"=>$category_selected->getTitle(),
                        "video_category"=>$imagineCacheManager->getBrowserPath( $category_selected->getMedia()->getLink(), 'category_thumb_api'),
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"]
                        );
            
            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();

            $message_notif = $this->send_notification(null, $message,$key); 
            
            $this->addFlash('success', 'Operation has been done successfully ');

        }
        return $this->render('AppBundle:Home:notif_category.html.twig',array(
          "form"=>$form->createView()
          ));
    }
   public function notifUrlAction(Request $request)
    {
    
        memory_get_peak_usage();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $em=$this->getDoctrine()->getManager();

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)      
            ->add('url', UrlType::class,array("label"=>"Url"))
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = array(
                        "type"=>"link",
                        "id"=>strlen($data["url"]),
                        "link"=>$data["url"],
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"]
                        );
                        $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_notif = $this->send_notification(null, $message,$key); 
           
            $this->addFlash('success', 'Operation has been done successfully ');
          
        }
        return $this->render('AppBundle:Home:notif_url.html.twig',array(
            "form"=>$form->createView()
        ));
    }


    public function notifWallpaperAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', 'entity', array('class' => 'AppBundle:Wallpaper'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_wallpaper = $em->getRepository("AppBundle:Wallpaper")->find($data["object"]);
            $original = "";
            $image = "";
            $thumbnail = "";
            $type = "";
            $extension = "";
            if ($selected_wallpaper->getVideo()) {
                  $type=$selected_wallpaper->getVideo()->getType();
                  $extension=$selected_wallpaper->getVideo()->getExtension();
            }else{
                  $type=$selected_wallpaper->getMedia()->getType();
                  $extension=$selected_wallpaper->getMedia()->getExtension();
            }
            $thumbnail= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_thumb_api');
            $image= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_image_api');
            if ($selected_wallpaper->getVideo()) {
                  if ($selected_wallpaper->getVideo()->getEnabled()) {
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getVideo()->getLink()) ;
                  }else{
                        $original = $selected_wallpaper->getVideo()->getLink();
                  } 
            }else{
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getMedia()->getLink()) ;
            }
            $message = array(
                  "type"=> "wallpaper",
                  "kind"=> $selected_wallpaper->getType(),
                  "id"=> $selected_wallpaper->getId(),
                  "wallpaper_kind"=>$selected_wallpaper->getType(),
                  "wallpaper_title"=>$selected_wallpaper->getTitle(),
                  "wallpaper_description"=>$selected_wallpaper->getDescription(),
                  "wallpaper_review"=>$selected_wallpaper->getReview(),
                  "wallpaper_premium"=>$selected_wallpaper->getPremium(),
                  "wallpaper_color"=>$selected_wallpaper->getColor(),
                  "wallpaper_size"=>$selected_wallpaper->getSize(),
                  "wallpaper_resolution"=>$selected_wallpaper->getResolution(),
                  "wallpaper_comment"=>$selected_wallpaper->getComment(),
                  "wallpaper_comments"=>sizeof($selected_wallpaper->getComments()),
                  "wallpaper_downloads"=>$selected_wallpaper->getDownloads(),
                  "wallpaper_views"=>$selected_wallpaper->getViews(),
                  "wallpaper_shares"=>$selected_wallpaper->getShares(),
                  "wallpaper_sets"=>$selected_wallpaper->getSets(),
                  "wallpaper_trusted"=>$selected_wallpaper->getUser()->getTrusted(),
                  "wallpaper_user"=>$selected_wallpaper->getUser()->getName(),
                  "wallpaper_userid"=>$selected_wallpaper->getUser()->getId(),
                  "wallpaper_userimage"=>$selected_wallpaper->getUser()->getImage(),
                  "wallpaper_type"=>$type,
                  "wallpaper_extension"=>$extension,

                  "wallpaper_thumbnail"=> $thumbnail,
                  "wallpaper_image"=> $image,
                  "wallpaper_original"=>$original,
                  "wallpaper_created"=>$selected_wallpaper->getCreated()->format("y/m/d H:i"),
                  "wallpaper_tags"=>$selected_wallpaper->getTags(),
                  "title"=> $data["title"],
                  "message"=>$data["message"],
                  "image"=> $data["image"],
                  "icon"=>$data["icon"]
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_notif = $this->send_notification(null, $message,$key); 
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_wallpaper.html.twig',array(
          "form"=>$form->createView()
          ));
    }

  public function notifUserWallpaperAction(Request $request)
    {
        memory_get_peak_usage();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $wallpaper_id= $request->query->get("wallpaper_id");
        $em=$this->getDoctrine()->getManager();

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('object', HiddenType::class,array("attr"=>array("value"=>$wallpaper_id)))
            ->add('message', TextareaType::class)
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            $selected_wallpaper = $em->getRepository("AppBundle:Wallpaper")->find($data["object"]);

            $user= $selected_wallpaper->getUser();

            $devices= $em->getRepository('AppBundle:Device')->findAll();
             if ($user==null) {
                throw new NotFoundHttpException("Page not found");  
            }
            $tokens=array();

            $tokens[]=$user->getToken();
            $data = $form->getData();
            $original = "";
            $image = "";
            $thumbnail = "";
            $type = "";
            $extension = "";
            if ($selected_wallpaper->getVideo()) {
                  $type=$selected_wallpaper->getVideo()->getType();
                  $extension=$selected_wallpaper->getVideo()->getExtension();
            }else{
                  $type=$selected_wallpaper->getMedia()->getType();
                  $extension=$selected_wallpaper->getMedia()->getExtension();
            }
            $thumbnail= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_thumb_api');
            $image= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_image_api');
            if ($selected_wallpaper->getVideo()) {
                  if ($selected_wallpaper->getVideo()->getEnabled()) {
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getVideo()->getLink()) ;
                  }else{
                        $original = $selected_wallpaper->getVideo()->getLink();
                  } 
            }else{
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getMedia()->getLink()) ;
            }
            $message = array(
                  "type"=> "wallpaper",
                  "kind"=> $selected_wallpaper->getType(),
                  "id"=> $selected_wallpaper->getId(),
                  "wallpaper_kind"=>$selected_wallpaper->getType(),
                  "wallpaper_title"=>$selected_wallpaper->getTitle(),
                  "wallpaper_description"=>$selected_wallpaper->getDescription(),
                  "wallpaper_review"=>$selected_wallpaper->getReview(),
                  "wallpaper_premium"=>$selected_wallpaper->getPremium(),
                  "wallpaper_color"=>$selected_wallpaper->getColor(),
                  "wallpaper_size"=>$selected_wallpaper->getSize(),
                  "wallpaper_resolution"=>$selected_wallpaper->getResolution(),
                  "wallpaper_comment"=>$selected_wallpaper->getComment(),
                  "wallpaper_comments"=>sizeof($selected_wallpaper->getComments()),
                  "wallpaper_downloads"=>$selected_wallpaper->getDownloads(),
                  "wallpaper_views"=>$selected_wallpaper->getViews(),
                  "wallpaper_shares"=>$selected_wallpaper->getShares(),
                  "wallpaper_sets"=>$selected_wallpaper->getSets(),
                  "wallpaper_trusted"=>$selected_wallpaper->getUser()->getTrusted(),
                  "wallpaper_user"=>$selected_wallpaper->getUser()->getName(),
                  "wallpaper_userid"=>$selected_wallpaper->getUser()->getId(),
                  "wallpaper_userimage"=>$selected_wallpaper->getUser()->getImage(),
                  "wallpaper_type"=>$type,
                  "wallpaper_extension"=>$extension,

                  "wallpaper_thumbnail"=> $thumbnail,
                  "wallpaper_image"=> $image,
                  "wallpaper_original"=>$original,
                  "wallpaper_created"=>$selected_wallpaper->getCreated()->format("y/m/d H:i"),
                  "wallpaper_tags"=>$selected_wallpaper->getTags(),
                  "title"=> $data["title"],
                  "message"=>$data["message"],
                  "image"=> $data["image"],
                  "icon"=>$data["icon"]
                );
            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
             $message_notif = $this->send_notificationToken($tokens, $message,$key); 
             $this->addFlash('success', 'Operation has been done successfully ');
             return $this->redirect($this->generateUrl('app_wallpaper_index'));
        }else{
           $video= $em->getRepository("AppBundle:Wallpaper")->find($wallpaper_id);
        }
        return $this->render('AppBundle:Home:notif_user_wallpaper.html.twig',array(
            "form"=>$form->createView()));
    }  
   
    public function settingsAction(Request $request)
    {   
        $em=$this->getDoctrine()->getManager();
        $settings=$em->getRepository("AppBundle:Settings")->findOneBy(array());
        if ($settings==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(new SettingsType(),$settings);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render('AppBundle:Home:settings.html.twig',array(
            "form"=>$form->createView()));
    }  
    public function indexAction(Request $request)
    {   

        $em=$this->getDoctrine()->getManager();
        $supports_count= $em->getRepository("AppBundle:Support")->count();
        $devices_count= $em->getRepository("AppBundle:Device")->count();
        $video_count= $em->getRepository("AppBundle:Wallpaper")->count("video");
        $image_count= $em->getRepository("AppBundle:Wallpaper")->count("image");
        $gif_count= $em->getRepository("AppBundle:Wallpaper")->count("gif");
        $review_count= $em->getRepository("AppBundle:Wallpaper")->countReview();
        $count_downloads= $em->getRepository("AppBundle:Wallpaper")->countDownloads();
        $count_views= $em->getRepository("AppBundle:Wallpaper")->countViews();
        $count_shares= $em->getRepository("AppBundle:Wallpaper")->countShares();
        $count_sets= $em->getRepository("AppBundle:Wallpaper")->countSets();

        $category_count= $em->getRepository("AppBundle:Category")->count();
        $comment_count= $em->getRepository("AppBundle:Comment")->count();
        $pack_count= $em->getRepository("AppBundle:Pack")->count();
        $color_count= $em->getRepository("AppBundle:Color")->count();
        $version_count= $em->getRepository("AppBundle:Version")->count();
        $users= $em->getRepository("UserBundle:User")->findAll();
        $users_count= sizeof($users)-1;





        return $this->render('AppBundle:Home:index.html.twig',array(
            
                "count_views"=>$count_views,
                "count_downloads"=>$count_downloads,
                "devices_count"=>$devices_count,
                "video_count"=>$video_count,
                "image_count"=>$image_count,
                "gif_count"=>$gif_count,
                "count_shares"=>$count_shares,
                "count_sets"=>$count_sets,
                "category_count"=>$category_count,

                "review_count"=>$review_count,
                "users_count"=>$users_count,
                "comment_count"=>$comment_count,

                "version_count"=>$version_count,
                "pack_count"=>$pack_count,
                "color_count"=>$color_count,
                "supports_count"=>$supports_count

        ));
    }
    public function api_deviceAction($tkn,$token){
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $d=$em->getRepository('AppBundle:Device')->findOneBy(array("token"=>$tkn));
        if ($d==null) {
            $device = new Device();
            $device->setToken($tkn);
            $em->persist($device);
            $em->flush();
            $message="Deivce added";
        }else{
            $message="Deivce Exist";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }

    public function tagsAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
         $q="  ";
        if ($request->query->has("q") and $request->query->get("q")!="") {
           $q.=" AND  w.title like '%".$request->query->get("q")."%'";
        }
        $dql        = "SELECT t FROM AppBundle:Tag t ORDER BY t.search desc ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $tags = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            12
        );
        $tags_list=$em->getRepository('AppBundle:Tag')->findAll();
        $tags_count= sizeof($tags_list);
        return $this->render('AppBundle:Home:tags.html.twig',array("tags"=>$tags,"tags_count"=>$tags_count));    
    }
    public function adsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(new AdsType(), $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:ads.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function deletetagAction(Request $request,$id)
    {
        $em         = $this->getDoctrine()->getManager();
        $support    = $em->getRepository('AppBundle:Tag')->find($id);
        if ($support==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->add('Yes', 'submit')
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($support);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success','Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_home_tags_index'));
        }
        return $this->render("AppBundle:Home:delete_tag.html.twig",array("form"=>$form->createView()));
    } 



}