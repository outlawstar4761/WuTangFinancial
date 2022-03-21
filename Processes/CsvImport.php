<?php

require_once __DIR__ . '/../Models/Transaction.php';

abstract class CsvImport{

  const DEBUG = true;
  const CREATEDBY = '\WuTang\CsvImport';
  const NOCSV = 'Source file not found.';
  const VERIFYACTMSG = "This csv appears to be ill-formated\nDue to reason: Missing or misplaced account type.\n**Please Check your code before you wreck your code.**\n";
  const VERIFYACNMSG = "This csv appears to be ill-formated\nDue to reason: Missing or misplaced account Number.\n**Please Check your code before you wreck your code.**\n";
  const VERIFYDATEMSG = 'Importing Csv with missing or misplaced date range str';
  const HEADERPATT = '/:\s(.*)/';

  protected $_sourceFile;
  protected $_csv;
  protected $_accountTypeStr;
  protected $_accountNumberStr;
  protected $_dateRangeStr;
  protected $_transactions = array();
  protected $_accountId;

  public $exceptions = array();

  protected function _getCsv(){
    if(!file_exists($this->_sourceFile)){
      throw new \Exception(self::NOCSV);
    }
    $this->_csv = array_map('str_getcsv',file($this->_sourceFile));
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
  protected function _parseHeaderValue($headerStr){
    preg_match(self::HEADERPATT,$headerStr,$matches);
    return $matches[1];
  }
}
