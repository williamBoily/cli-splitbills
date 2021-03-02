<?php

namespace Splitbills\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Splitbills\GoogleSheetAPI;
use Splitbills\TransactionReader;
use Splitbills\DateRange;
use Splitbills\NoDateRange;

class ExportTransactionsCommand extends Command
{
	// the name of the command (the part after "bin/console")
	protected static $defaultName = 'export-transactions';

	public function __construct(/*bool $requirePassword = false*/)
	{
		// best practices recommend to call the parent constructor first and
		// then set your own properties. That wouldn't work in this case
		// because configure() needs the properties set in this constructor
		
		//$this->requirePassword = $requirePassword;

		parent::__construct();
	}

	protected function configure()
	{
		$this
		// the short description shown while running "php bin/console list"
		->setDescription('Export transactions.')

		// the full command description shown when running the command with
		// the "--help" option
		->setHelp('Export transactions read from text file to google sheet in google drive')
		//->addArgument('password', $this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password')
		//->addArgument('clear', InputArgument::OPTIONAL, 'Clear existing Transactions in destination before exporting')
		->addOption('clear', 'c', InputOption::VALUE_NONE, 'Clear existing transactions from destination before exporting')
		->addOption('date-range', 'd', InputOption::VALUE_REQUIRED, 'Transactions only form this Date range will be exported to YYYY/MM/DD-YYYY/MM/DD')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$project_path = dirname(__FILE__, 3);

		$sheet_id = $_ENV['DEV_GOOGLE_SHEET_ID'];
		//$sheet_id = $_ENV['PROD_GOOGLE_SHEET_ID'];
		$range = $_ENV['GOOGLE_SHEET_RANGE'];

		$clearExistingData = (null === $input->getOption('clear'));
		$date_range = new NoDateRange();
		if(null !== $date_range_input = $input->getOption('date-range')){
			if(1 === preg_match('/([\d]{4}\/[\d]{2}\/[\d]{2})-([\d]{4}\/[\d]{2}\/[\d]{2})/', $date_range_input, $matches)){
				$range_start = $matches[1];
				$range_end = $matches[2];
				//YYYY/MM/DD
				$range_start = \DateTime::createFromFormat('Y/m/d|', $range_start, new \DateTimeZone('-0500'));
				$range_end = \DateTime::createFromFormat('Y/m/d|', $range_end, new \DateTimeZone('-0500'));
				$date_range = new DateRange($range_start, $range_end);
			}
		}

		try {
			$transactionReader = new TransactionReader($project_path . '/transactions');
			$transactions = $transactionReader->getTransactions($date_range);

			$google_auth_config = json_decode(file_get_contents($project_path . '/config/service_account_credentials.json'), true);
			$sheet_API = new GoogleSheetAPI($google_auth_config);
			$sheet_API->write_transactions($transactions, $sheet_id, $range, $clearExistingData);
			$commandResult = Command::SUCCESS;
		} catch (\Throwable $th) {
			$output->writeln('Error:');
			$output->writeln('message: ' . $th->getMessage());
			$output->writeln('line: ' . $th->getLine());
			$output->writeln('trace: ' . $th->getTraceAsString());
			$commandResult = Command::FAILURE;
		}

		// this method must return an integer number with the "exit status code"
		// of the command. You can also use these constants to make code more readable

		// return this if there was no problem running the command
		// (it's equivalent to returning int(0))
		return $commandResult;

		// or return this if some error happened during the execution
		// (it's equivalent to returning int(1))
		// return Command::FAILURE;
	}
}
