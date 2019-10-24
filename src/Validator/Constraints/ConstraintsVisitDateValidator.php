<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintsVisitDateValidator extends ConstraintValidator
{
	/**
	 * @param            $protocol
	 * @param Constraint $constraint
	 *
	 * @return mixed
	 */
	public function validate($protocol, Constraint $constraint)
	{
		//escape Constraint by empty Object
		if (null === $protocol || '' === $protocol) {
			return ;
		}

		$date = new \DateTime();
		//charge current DateTime zone in paris
		$date->setTimezone(new \DateTimeZone('Europe/Paris'));

		//Add violation when the user order Ticket after 14h00 in the same day
		if ($date->format('d/m/Y') === $protocol->getReservedFor()->format('d/m/Y')
			&& $date->format('H:i:s') > '13:58:00'
			&& $protocol->getFullDay() === True) {

			//atPath() use the name of the property in his FomType(OrderType)
			$this->context->buildViolation($constraint->message)
			              ->atPath('reservedFor')
			              ->addViolation();
		}
		//Add Violation for holidays
		foreach($constraint->holidays as $holiday) {
			if ($protocol->getReservedFor()->format('d/m') === $holiday) {
				$this->context->buildViolation($constraint->messageHolidaysSelected)
				              ->atPath('reservedFor')
				              ->addViolation();
			}
		}
	}
}
