<?php

namespace Splitbills;

class NoDateRange implements DateRangeInterface
{
	/**
	 * @inheritDoc
	 */
	public function isInRange(\DateTimeInterface $date) : bool
	{
		return true;
	}
}
