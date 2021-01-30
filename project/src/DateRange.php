<?php

namespace Splitbills;

class DateRange implements DateRangeInterface
{
	public $start;
	public $end;

	public function __construct(\DateTimeInterface $start, \DateTimeInterface $end)
	{
		$this->start = \DateTimeImmutable::createFromInterface($start);
		$this->$end = \DateTimeImmutable::createFromInterface($end);
	}

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
	public function isInRange(\DateTimeInterface $date) : bool
	{
		return ($date >= $this->start && $date < $this->end);
	}
}
