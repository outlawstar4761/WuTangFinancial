<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class Transaction extends Record{
  const DB = 'Wu_2';
  const TABLE = 'transactions';
  const PRIMARYKEY = 'id';

  public $id;
  public $transaction_type;
  public $date;
  public $description;
  public $memo;
  public $amount;
  public $balance;
  public $check_number;
  public $income;
  public $created_by;
  public $category;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
  public static function getUncategorized(){
    $ids = array();
    $data = array();
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select(self::PRIMARYKEY)
      ->where("category","is",null)
      ->get();
    while($row = mysqli_fetch_assoc($results)){
      $data[] = new self($row[self::PRIMARYKEY]);
    }
    return $data;
  }
}
