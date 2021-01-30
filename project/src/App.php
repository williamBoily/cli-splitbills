<?php

namespace Splitbills;

use Splitbills\GoogleSheetAPI;
use Splitbills\TransactionReader;

class App
{
	public function run(array $argv)
	{
		$clearExistingData = (isset($argv[1]) && $argv[1] === 'clear');

		$project_path = dirname(__FILE__, 2);

		$sheet_id = $_ENV['DEV_GOOGLE_SHEET_ID'];
		//$sheet_id = $_ENV['PROD_GOOGLE_SHEET_ID'];
		$range = $_ENV['GOOGLE_SHEET_RANGE'];

		$transactionReader = new TransactionReader($project_path . '/transactions');
		$transactions = $transactionReader->getTransactions();


		$google_auth_config = json_decode(file_get_contents($project_path . '/config/service_account_credentials.json'), true);
		$sheet_API = new GoogleSheetAPI($google_auth_config);
		$sheet_API->write_transactions($transactions, $sheet_id, $range, $clearExistingData);
	}
}




