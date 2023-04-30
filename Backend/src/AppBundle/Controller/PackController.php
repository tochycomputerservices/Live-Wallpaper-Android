<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Pack;
use AppBundle\Form\PackType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
class PackController extends Controller
{
    public function api_listAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $packs=$em->getRepository("AppBundle:Pack")->findBy(array(),array("created"=>"desc"));
        $list=array();
        foreach ($packs as $key => $pack) {
            if(sizeof($pack->getWallpapers())>0){
                $s["id"]=$pack->getId();
                $s["title"]=$pack->getTitle();
                $images= array();
                foreach ($pack->getWallpapers() as $key => $wall) {
                    $images[]=$imagineCacheManager->getBrowserPath($wall->getMedia()->getLink(), 'category_thumb_api');
                }
                $s["images"] =  $images;
                $list[]=$s;
            }
        }
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }
    public function api_byAction(Request $request,$id,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $wallpaper=$em->getRepository("AppBundle:Wallpaper")->find($id);
        if ($wallpaper==null) {
            throw new NotFoundHttpException("Page not found");  
        }
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $list=array();
        foreach ($wallpaper->getPacks() as $key => $pack) {
            $s["id"]=$pack->getId();
            $s["title"]=$pack->getTitle();
            $list[]=$s;
        }
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }
    public function addAction(Request $request)
    {
        $pack= new Pack();
        $form = $this->createForm(new PackType(),$pack);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pack);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_pack_index'));
        }
        return $this->render("AppBundle:Pack:add.html.twig",array("form"=>$form->createView()));
    }
    public function indexAction()
    {
	    $em=$this->getDoctrine()->getManager();
        $packs=$em->getRepository('AppBundle:Pack')->findBy(array(),array("created"=>"desc"));
	    return $this->render('AppBundle:Pack:index.html.twig',array("packs"=>$packs));    
	}
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $pack = $em->getRepository("AppBundle:Pack")->find($id);
        if($pack==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->add('Yes', 'submit')
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($pack);
            $em->flush();
            return $this->redirect($this->generateUrl('app_pack_index'));
        }
        return $this->render('AppBundle:Pack:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $pack=$em->getRepository("AppBundle:Pack")->find($id);
        if ($pack==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(new PackType(),$pack);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pack);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_pack_index'));
        }
        return $this->render("AppBundle:Pack:edit.html.twig",array("form"=>$form->createView()));
    }
}
?>