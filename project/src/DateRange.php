<?php

namespace Splitbills;

class DateRange implements DateRangeInterface
{
	public $start;
	public $end;

	public function __construct(\DateTime $start, \DateTime $end)
	{
		// php 8.0 only
		// $this->start = \DateTimeImmutable::createFromInterface($start);
		// $this->$end = \DateTimeImmutable::createFromInterface($end);
		$this->start = \DateTimeImmutable::createFromMutable($start);
		$this->end = \DateTimeImmutable::createFromMutable($end);
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
		return ($date->getTimestamp() >= $this->start->getTimestamp() && $date->getTimestamp() < $this->end->getTimestamp());
	}
}
