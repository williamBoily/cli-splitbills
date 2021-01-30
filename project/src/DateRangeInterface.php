<?php

namespace Splitbills;

interface DateRangeInterface
{
	/**
	 * Check if the date given is equals or after the start but before the end.
	 * 
	 * in other words:
	 *  - start comparaison is inclusive;
	 *  - end comparaison is exclusive;
	 *
	 * @param \DateTimeInterface $date
	 * @return boolean
	 */
	public function isInRange(\DateTimeInterface $date) : bool;
}
