<?php

require_once __DIR__ . '/../Models/Transaction.php';
require_once __DIR__ . '/../Models/TransactionTypeMap.php';
require_once __DIR__ . '/CsvImport.php';

class CheckingAccountImport2 extends CsvImport{

  const DESCPATT = '/([A-z]{1,}\s[A-z]{1,})/'; //match first two words
  const XFERPATT = '/Home\sbanking\sWithdrawal\sTo/'; //special pattern for online funds transfer
  const CSVKEYS = array(
    "ACNTNUM"=>0,
    "DATE"=>1,
    "CHECK"=>2,
    "MEMO"=>3,
    "DEBIT"=>4,
    "CREDIT"=>5,
    "STATUS"=>6,
    "BALANCE"=>7
  );
  const DESCRIPTION_EXCEPTIONS = array(
    'Check',
    'Home banking'
  );

  protected $_sourceFile;
  protected $_dateRangeStr;
  protected $_accountNumberStr;
  protected $_accountTypeStr;
  protected $_csv;
  protected $_transactions = array();

  public function __construct($sourceFile){
    $this->_sourceFile = $sourceFile;
    try{
      $this->_getCsv()->_buildTransactions();//->_getAccountInfo()->_verifyCsv();
    }catch(\Exception $e){
      throw new \Exception($e->getMessage());
    }
    //$this->_insert();
  }
  protected function _buildTransactions(){
    unset($this->_csv[0]); //remove the header row
    $this->_csv = array_values($this->_csv);
    foreach($this->_csv as $row){
      $descriptionArr = $this->_extractDescription($row[self::CSVKEYS['MEMO']]);
      if(is_null($row[self::CSVKEYS['DEBIT']]) || empty($row[self::CSVKEYS['DEBIT']])){
        $amount = $row[self::CSVKEYS['CREDIT']];
        $income = true;
      }else{
        $amount = number_format(($row[self::CSVKEYS['DEBIT']] * -1),2,'.','');
        $income = false;
      }
      $transaction = new Transaction();
      $transaction->transaction_type = $descriptionArr[1];
      $transaction->date = date('Y-m-d H:i:s',strtotime($row[self::CSVKEYS['DATE']]));
      $transaction->description = $descriptionArr[0];
      //Remove description and escape apostrophes
      $transaction->memo = trim(preg_replace('/' . $descriptionArr[0] . '/','',preg_replace('/\'/','',$row[self::CSVKEYS['MEMO']])));
      $transaction->amount = $amount;
      $transaction->balance = $row[self::CSVKEYS['BALANCE']];
      $transaction->check_number = $row[self::CSVKEYS['CHECK']];
      $transaction->income = $income;
      $transaction->created_by = parent::CREATEDBY;
      $this->_transactions[] = $transaction;
    }
    return $this;
  }
  protected function _extractDescription($memoStr){
    //New CSV has 'memo' and 'description' combined
    //Extract 'description' from 'Memo'
    if(!preg_match(self::DESCPATT,$memoStr,$matches)){
      if($memoStr == self::DESCRIPTION_EXCEPTIONS[0]){
        $matches = array(self::DESCRIPTION_EXCEPTIONS[0]);
      }else{
        throw new \Exception('Unable to identify description: ' . $memoStr);
      }
    }
    if(!array_key_exists($matches[0],TransactionTypeMap::TYPES)){
      throw new \Exception('Unable to identify description: ' . $memoStr);
    }
    return TransactionTypeMap::TYPES[$matches[0]];
  }
  protected function _morphField($value){
    return strtolower(preg_replace(self::SPACEPATT,self::SPACEREPL,$value));
  }
}
