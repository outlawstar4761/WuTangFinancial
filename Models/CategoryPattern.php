<?php

require_once __DIR__ . '/../Libs/Record/Record.php';

class CategoryPattern extends Record{

  const DB = 'Wu_2';
  const TABLE = 'category_patterns';
  const PRIMARYKEY = 'id';

  public $id;
  public $category_id;
  public $label;
  public $pattern;

  public function __construct($id = null){
    parent::__construct(self::DB,self::TABLE,self::PRIMARYKEY,$id);
  }
}
