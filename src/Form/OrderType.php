<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitorsNbr', ChoiceType::class, [
	            'choices' => $this->getVisitorChoices()
            ])
	        ->add('fullDay', ChoiceType::class, [
		        'choices' => Booking::TYPE_TICKET
	        ])
            ->add('reservedFor', DateType::class, [
	            'widget' => 'single_text',
	            'input'  => 'datetime_immutable',
	            'days' => range(1,20),
	            'help' => 'Not allowed days'
            ])->add($builder->create(
            	'visitor', VisitorType::class, [
            	        'by_reference' => true,
		                'label' => false
	                    ]
	            )

	        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Booking::class,
	        'translation_domain' => 'forms'
        ]);
    }

	/**
	 * *create range for number of tickets choices
	 * @param int $start
	 * @param int $end
	 *
	 * @return int[]
	 */
	private function getVisitorChoices(int $start = 1, int $end = 10) :array
    {
    	$range = range($start, $end);
    	$choices = [];

    	foreach($range as $k => $v){
    		$choices[$v] = $k;
        }
        return $choices;
    }
}
