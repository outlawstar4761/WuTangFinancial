<?php

require_once __DIR__ . '/../Models/Transaction.php';

class CreditAccountImport{

  public function __construct($sourceFile){
    $this->_sourceFile = $sourceFile;
    try{
      $this->_getCsv()
           ->_getAccountInfo()
           ->_verifyCsv()
           ->_buildTransactions()
           ->_insert();
    }catch(Exception $ex){
      throw $ex;
    }
  }
  protected function _getAccountInfo(){
    $this->_accountTypeStr = $this->_csv[0][0];
    $this->_accountNumberStr = $this->_csv[1][0];
    $this->_dateRangeStr = $this->_csv[2][0];
    return $this;
  }
  protected function _buildTransactions(){
    for($i = 5; $i < count($this->_csv); $i++){
      if(empty($this->_csv[$i][0]) || is_null($this->_csv[$i][0])){
        break; //skip records with no date
      }
      $transaction = new Transaction();
      $transaction->transaction_type = $this->_csv[$i][1];
      $transaction->date = date('Y-m-d H:i:s',strtotime($this->_csv[$i][0]));
      $transaction->description = $this->_csv[$i][3];
      $transaction->memo = $this->_csv[$i][2];
      $transaction->amount = $this->_csv[$i][4];
      $transaction->created_by = parent::CREATEDBY;
    }
    return $this;
  }
}
