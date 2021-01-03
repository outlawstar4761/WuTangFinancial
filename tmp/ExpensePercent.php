<?php

// require_once __DIR__ . '/../Db/db.php';
require_once __DIR__ . '/Models/Transaction.php';

function _percent($value,$total){
  return ($value / $total) * 100;
}

$expense_data = Transaction::categoryBreakDown();
$income_data = Transaction::categoryBreakDown(false);
$yearlyExpenses = Transaction::getYearlyTotals();
$yearlyIncome = Transaction::getYearlyTotals(false);
unset($yearlyExpenses[1969]);

$output = array();
$years = array(2014,2015,2016,2017,2018,2019,2020);
foreach($years as $year){
  $yearlySum = $yearlyExpenses[$year];
  foreach($expense_data as $row){
    if($row['tyear'] == $year){
      // $output[$row['category']][$year] = abs($row['total_spent']);
      $output[$row['category']][$year] = round(_percent(abs($row['total_spent']),$yearlySum),2);
    }
  }
}

$collectedYears = array();
foreach($years as $targetYearInt){
  $targetYear = array();
  $percentSum = 0;
  foreach($output as $category=>$values){
    if(isset($values[$targetYearInt])){
      $targetYear[$category] = $values[$targetYearInt];
      $percentSum += $values[$targetYearInt];
    }
  }
  arsort($targetYear);
  $collectedYears[] = $targetYear;
  // print_r($targetYear);
  // echo "-------------------------";
  // echo "Total: % " . $percentSum . "\n";
}
print_r($collectedYears);

foreach($collectedYears as $index=>$yearData){
  $topValues = array_slice($yearData,0,5);
  $topValues['total_percent'] = array_sum(array_values($topValues));
  $topValues['yearly_expense'] = $yearlyExpenses[$years[$index]];
  $topValues['Year'] = $years[$index];
  print_r($topValues);
}
