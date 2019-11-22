<?php


require_once __DIR__ . '/Models/CategoryPattern.php';


$patterns = array(
  array("pattern"=>"EXTRA\sSPACE\sSTOR","label"=>"Extra Space Storage","category_id"=>0)
);

foreach($patterns as $pattern){
  $p = new CategoryPattern();
  foreach($pattern as $key=>$value){
    $p->$key = $value;
  }
  $p->create();
}
