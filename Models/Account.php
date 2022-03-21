<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class Account extends Record{
  const DB = 'Wu_2';
  const TABLE = 'accounts';
  const PRIMARYKEY = 'id';

  public $id;
  public $account_number;
  public $account_type;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
  public static function GetFromAccntNumber($accountNumberStr){
    $results = $GLOBALS['db']->database(self::DB)->table(self::TABLE)->select(self::PRIMARYKEY)->where("account_number","=","'" . $accountNumberStr . "'")->get();
    if(!mysqli_num_rows($results)){
      throw new Exception($accountNumberStr . " is invalid");
    }
    while($row = mysqli_fetch_assoc($results)){
      $id = $row[self::PRIMARYKEY];
    }
    return new self($id);
  }
}
