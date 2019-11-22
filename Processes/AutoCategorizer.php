<?php

require_once __DIR__ . '/../Models/Transaction.php';
require_once __DIR__ . '/../Models/TransactionCategory.php';

class AutoCategorizer{

  protected $_transactions = array();
  protected $_categories = array();
  protected $_patterns = array();

  public function __construct(){
    $this->_getTransactions()
         ->_getCategories()
         ->_getPatterns()
         ->_categorize();
    print_r($this->_categories);
    print_r($this->_patterns);
  }
  protected function _getTransactions(){
    $this->_transactions = Transaction::getUncategorized();
    return $this;
  }
  protected function _getCategories(){
    $this->_categories = TransactionCategory::getAll();
    return $this;
  }
  protected function _getPatterns(){
    foreach($this->_categories as $category){
      $this->_patterns[] = $category->getPatterns();
    }
    return $this;
  }
  protected function _categorize(){
    foreach($this->_transactions as $transaction){

    }
    return $this;
  }
  protected function _isMatch($pattern,$str){
    if(preg_match($pattern,$str)){
      return true;
    }
    return false;
  }

}
