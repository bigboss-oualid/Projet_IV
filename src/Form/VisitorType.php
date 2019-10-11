<?php

namespace App\Form;

use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('lastname', TextType::class)
	        ->add('firstname', TextType::class)
	        ->add('country', CountryType::class, [
		        //'placeholder' => 'Choose your Country',
		        'choice_attr' => function ($choice) {
			        //Add attribute selected to option 'FR'
			        if($choice === 'FR') {
				        return ['selected' => 'selected'];
			        }
			        return [];
		        }
	        ])
	        ->add('birthday', DateType::class, [
		        'widget' => 'single_text'
	        ])
	        ->add('discount', CheckboxType::class, [
		        'help' => 'Reduction rule',
		        'required' => false
	        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visitor::class,
	        'translation_domain' => 'forms'
        ]);
    }
}
