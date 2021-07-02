<?php

require_once __DIR__ . '/../Models/Transaction.php';

/*
records with no date are pending transactions, filter them out
*/

class CsvImport{

  const DEBUG = true;
  const NOCSV = 'Source file not found.';
  const DBKEY = 'created_by';
  const DBVAL = '\WuTang\CsvImport';
  const SPACEPATT = '/\s/';
  const SPACEREPL = '_';
  const VERIFYACTMSG = "This csv appears to be ill-formated\nDue to reason: Missing or misplaced account type.\n**Please Check your code before you wreck your code.**\n";
  const VERIFYACNMSG = "This csv appears to be ill-formated\nDue to reason: Missing or misplaced account Number.\n**Please Check your code before you wreck your code.**\n";
  const VERIFYDATEMSG = 'Importing Csv with missing or misplaced date range str';


  public $success;
  public $exceptions = array();

  protected $_csv;
  protected $_sourceFile;
  protected $_dateRangeStr;
  protected $_accountNumberStr;
  protected $_accountTypeStr;
  protected $_verified;
  protected $_transactions = array();

  public function __construct($sourceFile){
    // $this->_sourceFile = __DIR__ . '/../data/Export.csv';
    $this->_sourceFile = $sourceFile;
    try{
      $this->_getCsv()->_getAccountInfo()->_verifyCsv();
    }catch(\Exception $e){
      throw new \Exception($e->getMessage());
    }
    $this->_insert();
  }
  protected function _getCsv(){
    if(!file_exists($this->_sourceFile)){
      throw new \Exception(self::NOCSV);
    }
    $this->_csv = array_map('str_getcsv',file($this->_sourceFile));
    return $this;
  }
  protected function _getAccountInfo(){
    $i = 5;
    while($i--){
      if($i == 4){
        $this->_buildTransactions();
      }elseif($i == 3){
        continue;
      }elseif($i == 2){
        $this->_dateRangeStr = $this->_csv[$i][0];
      }elseif($i == 1){
        $this->_accountNumberStr = $this->_csv[$i][0];
      }elseif($i == 0){
        $this->_accountTypeStr = $this->_csv[$i][0];
      }
    }
    return $this;
  }
  protected function _buildTransactions(){
    $x = 0;
    for($i = 5; $i < count($this->_csv); $i++){
      $transaction = new Transaction();
      for($j = 0; $j <= 6; $j++){
        $key = $this->_morphField($this->_csv[4][$j]);
        if($j == 1){
          if(empty($this->_csv[$i][$j]) || is_null($this->_csv[$i][$j])){
            break 2;
          }
          $transaction->$key = date('Y-m-d H:i:s',strtotime($this->_csv[$i][$j]));
        }elseif($j == 3){
          $transaction->$key = preg_replace('/\'/','',$this->_csv[$i][$j]);
        }elseif($j == 6 && (empty($this->_csv[$i][$j]) || is_null($this->_csv[$i][$j]))){
          $transaction->$key = 0;
        }else{
          $transaction->$key = $this->_csv[$i][$j];
        }
        $transaction->created_by = self::DBVAL;
      }
      $this->_transactions[] = $transaction;
    }
    return $this;
  }
  protected function _verifyCsv(){
    if(empty($this->_accountTypeStr) || is_null($this->_accountTypeStr)){
      throw new \Exception(self::VERIFYACTMSG);
    }elseif(empty($this->_accountNumberStr) || is_null($this->_accountNumberStr)){
      throw new \Exception(self::VERIFYACNMSG);
    }elseif(empty($this->_dateRangeStr) || is_null($this->_dateRangeStr)){
      throw new \Exception(self::VERIFYDATEMSG);
    }else{
      $this->_verified = true;
    }
    return $this;
  }
  protected function _morphField($value){
    return strtolower(preg_replace(self::SPACEPATT,self::SPACEREPL,$value));
  }
  protected function _insert(){
    foreach($this->_transactions as $transaction){
      if(!Transaction::recordExists($transaction->date,$transaction->amount,$transaction->memo)){
        self::DEBUG ? print_r($transaction):$transaction->create();
      }else{
        $this->exceptions[] = $transaction;
      }
    }
    return $this;
  }
}
