<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('title',null,array("label"=>"Pack title"));
       $builder->add('enabled',null,array("label"=>"Pack enabled"));
       $builder->add('save', 'submit',array("label"=>"save"));
    }
    public function getName()
    {
        return 'Pack';
    }
}
?>