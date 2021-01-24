<?php
namespace Splitbills;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_ClearValuesRequest;

class GoogleSheetAPI
{
	private $sheet_service;

	public function __construct(array $auth_config)
	{
		$client = new Google_Client();
		
		$client->setAuthConfig($auth_config);
		//$client->setApplicationName("does_this_matter");
		$client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);
		
		$this->sheet_service = new Google_Service_Sheets($client);
	}

	public function write_transactions($transactions, $sheet_id, $range, $overwrite = false)
	{
		$requestBody = new Google_Service_Sheets_ValueRange();
		$requestBody->setMajorDimension('ROWS');
		$requestBody->setRange($range);
		$requestBody->setValues($this->transform_transactions_for_sheet($transactions));

		$params = [
			'responseValueRenderOption' => 'FORMATTED_VALUE',
			'insertDataOption' => 'INSERT_ROWS',
			'valueInputOption' => 'USER_ENTERED', // RAW
			'includeValuesInResponse' => false,
		];

		if($overwrite){
			$clearRequest = new Google_Service_Sheets_ClearValuesRequest();
			$this->sheet_service->spreadsheets_values->clear($sheet_id, $range, $clearRequest);
		}

		return $this->sheet_service->spreadsheets_values->append($sheet_id, $range, $requestBody, $params);
	}

	private function transform_transactions_for_sheet($transactions)
	{
		$transformed = [];
		foreach ($transactions as $transaction) {
			$transformed[] = [
				$transaction->date->format('Y-m-d'),
				$transaction->description,
				$transaction->amount
			];
		}

		return $transformed;
	}
}
