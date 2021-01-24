<?php
namespace Splitbills;

class Transaction
{
	public $amount;
	public $date;
	public $description;

	public function __construct(float $amount, \DatetimeImmutable $date, string $description)
	{
		$this->amount = $amount;
		$this->date = $date;
		$this->description = $description;
	}
}
