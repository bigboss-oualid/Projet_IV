<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintsVisitDate extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = "Today you can't choose a full day ticket";

    public $holidays;
    public $messageHolidaysSelected = "Sorry!! for the chosen day the museum will be closed, you can choose another day for your visit";


    //Add Constraint to Class Booking
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
