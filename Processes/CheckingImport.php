<?php

require_once __DIR__ . '/../Models/Transaction.php';

class CheckingAccountImport{

  const DEBUG = true;

  const DBKEY = 'created_by';
  const SPACEPATT = '/\s/';
  const SPACEREPL = '_';

  public function __construct($sourceFile){
    $this->_sourceFile = $sourceFile;
    try{
      $this->_getCsv()->_getAccountInfo()->_verifyCsv();
    }catch(\Exception $e){
      throw new \Exception($e->getMessage());
    }
    $this->_insert();
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
        $transaction->created_by = parent::CREATEDBY;
      }
      $this->_transactions[] = $transaction;
    }
    return $this;
  }
  protected function _morphField($value){
    return strtolower(preg_replace(self::SPACEPATT,self::SPACEREPL,$value));
  }
}
