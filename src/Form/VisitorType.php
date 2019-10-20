<?php

namespace App\Form;

use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('lastname', TextType::class, ['required' => false])
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
	        ->add('birthday', BirthdayType::class, [
		        'widget' => 'single_text',
		        'html5' => false,//disabled type=date from <input>
		        'input' => 'datetime',//store input as Datetime in the Object
		        'format' => 'dd/mm/yyyy',//same format as datepicker (JS)

		        'attr' => [
			        'class' => 'datepicker-ticket',
			        'autocomplete' => 'off',
		        ],
		        'help' => 'Not allowed days'
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
	        'translation_domain' => 'forms',
        ]);
    }
}
