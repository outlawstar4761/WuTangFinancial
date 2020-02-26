<?php

require_once __DIR__ . '/../Models/Transaction.php';

//select count(*) as total,date,amount,memo from Wu_2.transactions group by date,amount,memo having total > 1 order by date
//foreach dupe record, get the first one with matching total,date,amount,memo and delete it.
class DupeFinder{

  public $pruned;

  protected $_dupeRecords = array();

  public function __construct(){
    $this->pruned = 0;
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
      ->get();
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
    $GLOBALS['db']
      ->database(Transaction::DB)
      ->table(Transaction::TABLE)
      ->select(Transaction::PRIMARYKEY)
      ->where('amount','=',"'" . $amount . "'")
      ->andWhere('date','=',"'" . $date . "'");
    if(is_null($memo)){
      $GLOBALS['db']->andWhere("memo","is","null");
    }else{
      $GLOBALS['db']->andWhere("memo","=","'" . $memo . "'");
    }
    $results = $GLOBALS['db']->orderBy(Transaction::PRIMARYKEY . " limit 1")->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('Cannot find duplicate with: ' . $date . ' ' . $amount . ' ' . $memo);
    }
    while($row = mysqli_fetch_assoc($results)){
      $obj = new Transaction($row[Transaction::PRIMARYKEY]);
    }
    return $obj;
  }
  protected function _pruneDuplicates(){
    foreach($this->_dupeRecords as $row){
      $record = $this->_getTopDupe($row['date'],$row['amount'],$row['memo']);
      $record->delete();
      $this->pruned++;
    }
    return $this;
  }
}
