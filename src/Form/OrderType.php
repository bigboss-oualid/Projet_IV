<?php

namespace App\Form;

use App\Entity\Booking;
use App\Service\Cart\CartService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
	/**
	 * @var CartService
	 */
	private $cartService;

	public function __construct(CartService $cartService)
	{
		$this->cartService = $cartService;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitorsNbr', ChoiceType::class, [
	            'choices' => $this->getVisitorChoices(),
				'placeholder' => 'Choose number of visitors ?',
	            'required' => false

            ])
	        ->add('fullDay', ChoiceType::class, [
		        'choices' => Booking::FULL_DAY_TICKET,
	        ])
            ->add('reservedFor', DateType::class, [
	            'widget' => 'single_text',
	            'html5' => false,//disabled type=date from <input>
	            'format' => 'dd/MM/yyyy',//same format as JS pickadate but month with upper character small character give mistake
	            'attr' => [
		            'data-toggle' => 'datepicker-visit',
		            //'autocomplete' => 'off',
	            ]
            ])

	        ->add('visitors', CollectionType::class, [
	        	'attr' => [
	        		'data-visitors-nbr' => $this->cartService->getLastOrder()->getVisitorsNbr(),

		        ],
		        'label' => false,
		        'entry_type' => VisitorType::class,
		        'allow_add' => true,
		        'allow_delete' => true,
		        'error_bubbling' => false,
	        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Booking::class,
	        'translation_domain' => 'forms',
	        'validation_groups' => ['order', 'visitor']
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
		return array_combine($range, $range);
	}
}
