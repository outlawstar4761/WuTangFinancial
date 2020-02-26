<?php

require_once __DIR__ . '/../Models/Transaction.php';

//select count(*) as total,date,amount,memo from Wu_2.transactions group by date,amount,memo having total > 1 order by date
//foreach dupe record, get the first one with matching total,date,amount,memo and delete it.
class DupeFinder{

  protected $_dupeRecords = array();

  public function __construct(){
    $this->_getDuplicateRecords()->_pruneDuplicates();
  }
  protected function _getDuplicateRecords(){
    $results = $GLOBALS['db']
      ->database(Transaction::DB)
      ->table(Transaction::TABLE)
      ->select('count(*) as total,date,amount,memo')
      ->groupBy('date,amount,memo')
      ->having('total','>',1)
      ->orderBy('date')
    if(!mysqli_num_rows($results)){
      throw new \Exception('No Duplicate Records.');
    }
    while($row = mysqli_fetch_assoc($results)){
      $this->_dupeRecords[] = $row;
    }
    return $this;
  }
  protected function _getTopDupe($date,$amount,$memo){
    $obj = null;
    $results = $GLOBALS['db']
      ->database(Transaction::DB)
      ->table(Transaction::TABLE)
      ->select(Transaction::PRIMARYKEY)
      ->where('memo','=',"'" . $memo . "'")
      ->andWhere('amount','=',"'" . $amount . "'")
      ->andWhere('date','=',"'" . $date . "'")
      ->orderBy(Transaction::PRIMARYKEY)
      ->limit(1)
      ->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('Cannot find duplicate with: ' . $date . ' ' . $amount . ' ' . $memo);
    }
    while($row = mysqli_fetch_assoc($results)){
      $obj = new Transaction($row[Transaction::PRIMARYKEY]);
    }
    return $obj;
  }
  protected function _pruneDuplicates(){
    foreach($this->_dupeRecords as $record){
      $toDelete = $this->_getTopDupe($record['date'],$record['amount'],$record['memo']);
      print_r($record);
      print_r($toDelete);
    }
    return $this;
  }
}
