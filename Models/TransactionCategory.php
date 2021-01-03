<?php

require_once __DIR__ . '/../Libs/Record/Record.php';
require_once __DIR__ . '/CategoryPattern.php';

class TransactionCategory extends Record{
  const DB = 'Wu_2';
  const TABLE = 'transaction_categories';
  const PRIMARYKEY = 'id';

  public $id;
  public $category;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
  public static function getAll(){
    $data = array();
    $ids = parent::getAll(self::DB,self::TABLE,self::PRIMARYKEY);
    foreach($ids as $id){
      $data[] = new self($id);
    }
    return $data;
  }
  public function getPatterns(){
    return CategoryPattern::getCategory($this->id);
  }
  public static function getByLabel($label){
    $data = array();
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select(self::PRIMARYKEY)
      ->where('category','like',"'%" . $label . "%'")
      ->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    while($row = mysqli_fetch_assoc($results)){
      $data[] = new self($row[self::PRIMARYKEY]);
    }
    return $data;
  }
}
