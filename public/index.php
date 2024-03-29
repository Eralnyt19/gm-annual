<?php

// declare(strict_types = 1);

// echo $customer;

if (isSet($customer) == False) {

	$customer = "D";
}

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);




$income_list = ["Sjukersättning", "Merkostnad", "Habilitering", "Bostadbidrag", "Lön", "Räntor",
               "Skatteåterbäring", "Övrigt"];

$expense_list = ["Hyra", "Medicin", "Tfn TV", "Försäkring", "Privat", "Övrigt", "Skatt",
                "BankAvg/KvSkatt", "Arvode", "Arvodesskatt", "Färdtjänst", "FUB/ABF",
                "Vattenfall", "Spärrkonto"];

require APP_PATH . 'app.php';
require APP_PATH . 'helper.php';



$files = getTransactionFiles(FILES_PATH);


// Make visable for html
$incomeTable = [];
$expenseTable = [];
$transactions = [];

foreach($files as $file) {
	
	if(customerMatch($customer == NULL ? 'UNKNOWN' : $customer, $file)) {
			$inSaldo = getInSaldo($file);
			$transactions = getTransactions($file);
	}
}


if (count($transactions) > 0) {

	$incomeTable = getIncome($transactions, $income_list);
	$expenseTable = getExpense($transactions, $expense_list); 


	$totals = calculateTotals($inSaldo, $transactions);
	
	require VIEWS_PATH . 'transactions.php';

} else {

		echo " Ej känd kund";

}


?>
