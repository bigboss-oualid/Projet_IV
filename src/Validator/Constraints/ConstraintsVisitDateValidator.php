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
		$today = $date->format('d/m/Y');
		$reservedDay = $protocol->getReservedFor()->format('d/m/Y');
		//Can't pay ticket in the past
		if ($today > $reservedDay) {
			$this->context->buildViolation($constraint->pastMessage)
			              ->atPath('reservedFor')
			              ->addViolation();
		}
		//Add violation when the user order Ticket after 14h00 in the same day
		if ($today === $reservedDay && $date->format('H:i:s') > '13:58:00'
			&& $protocol->getFullDay() === True) {

			//atPath() use the name of the property in his FomType(OrderType)
			$this->context->buildViolation($constraint->afternoonMessage)
			              ->atPath('reservedFor')
			              ->addViolation();
		}
	}
}
