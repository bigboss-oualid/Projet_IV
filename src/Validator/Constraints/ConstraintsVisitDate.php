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


    //Add Constraint to Class Booking
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
