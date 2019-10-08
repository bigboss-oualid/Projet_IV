<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('reservedFor', DateType::class, [
	            'widget' => 'single_text',
	            'input'  => 'datetime_immutable',
	            'days' => range(1,20),
	            'help' => 'C’est pas possible de réserver pour les jours passés, les mardis, les dimanches, et les jours fériés',
            ])
            ->add('fullDay', ChoiceType::class, [
	            'choices' => Booking::TYPE_TICKET
            ])
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
