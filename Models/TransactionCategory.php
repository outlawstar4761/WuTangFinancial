<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class TransactionCategory extends Record{
  const DB = 'Wu_2';
  const TABLE = 'transaction_categories';
  const PRIMARYKEY = 'id';

  public $id;
  public $category;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
}
