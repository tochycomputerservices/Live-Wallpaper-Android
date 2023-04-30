<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class WallpaperReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('title',"text",array());
       $builder->add('description',null,array("label"=>"description"));
         $builder->add('premium',null,array("label"=>"Premium"));

       $builder->add('comment');
       $builder->add('tags');
       $builder->add("color");

       $builder->add("categories",'entity',
                    array(
                          'class' => 'AppBundle:Category',
                          'expanded' => true,
                          "multiple" => "true",
                          'by_reference' => false
                        )
                    );
       $builder->add("colors",'entity',
                    array(
                          'class' => 'AppBundle:Color',
                          'expanded' => true,
                          "multiple" => "true",
                          'by_reference' => false
                        )
                    );
      $builder->add("packs",'entity',
                      array(
                            'class' => 'AppBundle:Pack',
                            'expanded' => true,
                            "multiple" => "true",
                            'by_reference' => false
                          )
                      );
       $builder->add('save', 'submit',array("label"=>"REVIEW"));
    }
    public function getName()
    {
        return 'Status';
    }
}
?>