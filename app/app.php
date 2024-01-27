<?php


// month_list = ["DUM", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]




$tableHead = ["Category", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Total"];

define ('INCOME' , 1) ; 
define ('EXPENSE' , 2) ; 
define ('DESCRIPTIONPOS' , 0) ; 
define ('TOTALPOS' , 13) ; 



function formatMonth(string $month) {

	$namesOfMonth = ["DUM", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

	$month = (int) $month;
	$month = $month++;  //start index at 1


	return $namesOfMonth[$month];

}

function formatDescription(array $tableRow, array $list) : array {
	
    
 		$tableRow[DESCRIPTIONPOS] = $list[(int) $tableRow[DESCRIPTIONPOS]];
     
     return $tableRow;   
}




function getTransactionFiles(string $dirPath): array {
	
	$files = [];

	foreach(scandir($dirPath) as $file) {
		if (is_dir($file)) {
			continue;
		}
		
	$files[] = $dirPath . $file;
	
	}
	

	return $files;
}


function customerMatch(string $customer, string $file) {
		
	
return basename($file, '.csv') == $customer;


}


function getInSaldo(string $fileName): float {

	if (! file_exists($fileName)) {
		trigger_error("file " . $fileName . " does not exist ", E_USER_ERROR);
	}
	
	$file = fopen($fileName, 'r');
	
	$transHead = fgetcsv($file);  
	
	
	fclose($file);
	return (float) $transHead[4];
}




function getTransactions(string $fileName) : array {

	if (! file_exists($fileName)) {
		trigger_error("file " . $fileName . " does not exist ", E_USER_ERROR);
	}
	
	$file = fopen($fileName, 'r');
	
	fgetcsv($file);  // discard
	
	
	
	
	$transactions = [];
	
	while (($transaction  = fgetcsv($file)) !== false ) {
		
		$transaction = extractTransaction($transaction);
		
		$transactions[] = $transaction;
	}
	
	fclose($file);
	
	return $transactions;
}






function extractTransaction(array $transactionRow): array {


	[$sequence, $month, $type, $description, $amount] = $transactionRow;
	

	$amount =  (float) preg_replace("/[^-0-9\.]/","", $amount);
	if ($type == EXPENSE) { $amount = -$amount;}
	
	// Discard sequence and add customer
	
	return [
		'DUMMY',
		'month'	=> $month,
		'type'  => $type,
		'description'  => $description,
		'amount'  => $amount,
	];

}



function getIncome(array $transactions,array $list){

	$income_list = $list;
	
	return getTable($transactions, INCOME, $income_list );
}




function getExpense(array $transactions, array $list){
	$expense_list = $list;
	return getTable($transactions, EXPENSE, $expense_list );
}




function getTable(array $transactions, string $type, array $list){

	$table = [];
	$lenght= count($list);
	
	for ($description = 0; $description < $lenght; $description++) {
	
		$tableRow = getTableRow($transactions, $type, $description);
				
		$tableRow   = formatDescription($tableRow, $list );
				
		$table[] = $tableRow;
			
	}

	return $table;
}



function getTableRow(array $transactions, string $type, string $description) {

	
	$category = [];
	$category[DESCRIPTIONPOS] = $description;


	// Initiate
	for ($i = 1; $i <=TOTALPOS; $i++ )  {
		$category[$i] = 0.0;
	}


	for ($m = 1; $m <=12; $m++ ) {
		
		foreach($transactions as $transaction ) {
			
	
			if ($transaction['type'] == $type) 	 {	
				if ($transaction['description'] == $description) {
					if ((int) $transaction['month'] == $m) {		

					$category[$m] += $transaction['amount'];
					$category[TOTALPOS] += $transaction['amount'];
					}
					
				}	
			}	
				
		}  // foreach
	} // for month
	
	return $category;
}



function calculateTotals(float $inSaldo, array $transactions): array {
	$totals = ['inSaldo' => 0, 'netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];
	
	$totals['inSaldo'] =  $inSaldo ;
	$totals['netTotal'] =  $inSaldo ;
	
	foreach($transactions as $transaction) {
		$totals['netTotal'] += $transaction['amount'];
		
		if($transaction['amount'] >= 0) {
			$totals['totalIncome'] += $transaction['amount'];
		} else {
			$totals['totalExpense'] += $transaction['amount'];	
		}
	}

	return $totals;
}
	




?>
