<?php

namespace Splitbills;

use DateTimeImmutable;
use DateTimeZone;

use Splitbills\DateRangeInterface;

class TransactionReader
{
	protected $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	public function getTransactions(DateRangeInterface $date_range){
		$files = scandir($this->directory, SCANDIR_SORT_DESCENDING);
	
		$extension = '.txt';
		$offset = strlen($extension);
		foreach ($files as $key => $file) {
			if($extension !== substr($file, -$offset)){
				unset($files[$key]);
			}
		}
	
		$file = reset($files);
		$transactions = [];
	
		$fn = fopen($this->directory . '/' . $file, "r");
	
		$transactions_started = false;
		while(! feof($fn)) {
			$line = fgets($fn);

			if(!$transactions_started && $this->is_transactions_list_reached($line)){
				$transactions_started = true;
				continue;
			}

			if($transactions_started){
				if($this->is_transactions_list_ended($line)){
					break;
				}
				
				$parts = preg_split('/\t+/', $line);
				$amount = $this->convert_string_amount_to_float($parts[4]);
				$date = DateTimeImmutable::createFromFormat('j M Y', $parts[0], new DateTimeZone("-0500"));

				if($date_range->isInRange($date)){
					$transactions[] = new Transaction($amount, $date, $parts[3]);
				}
	
			}
		}
		fclose($fn);
	
		return $transactions;
	}

	private function is_transactions_list_reached(string $line)
	{
		// only support files in english to work only with 1 way for numbers and dates
		if(strpos($line, 'Transaction date') === 0){
			return true;
		}

		return false;
	}

	private function is_transactions_list_ended(string $line)
	{
		// when we know we are in the transactions list, all transaction lines start with a date.
		// as soon as we hit a line that does not start with a date, the transactions list is done.

		// start with a 0,1,2 or a 3, one time. => first digit of day of month with 2 digits 0x, ... 1x ... 2x ... 3x
		// any digits 1 time. => to complete the 2 digits number of the month x1, x2, ... x9
		// space
		// any non-digits three times => for the 3 letters month. use non-digit to cover cases of 'É' like in 'DÉC'
		// space
		// 20xx => years. good until 2099
		if(1 === preg_match('/^[0123]{1}[\d]{1} [\D]{3} 20[\d]{2}/', $line)){
			return false;
		}

		return true;
	}

	private function convert_string_amount_to_float(string $string_amount)
	{
		$string_amount = trim($string_amount);

		$sign = strpos($string_amount, 'CR') === 0 ? '-' : '+';
		// remove all characters that are not 0-9 and the dot(.)
		$string_amount = preg_replace('/[^\d.]*/', '', $string_amount);
		
		$amount = (float)$string_amount;
		if($sign == '-'){
			$amount = -$amount; 
		}

		return $amount;
	}
}
