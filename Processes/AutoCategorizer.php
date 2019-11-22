<?php

require_once __DIR__ . '/../Models/Transaction.php';
require_once __DIR__ . '/../Models/TransactionCategory.php';

class AutoCategorizer{

  public $matches = array();
  protected $_transactions = array();
  protected $_categories = array();
  protected $_patterns = array();

  public function __construct(){
    $this->_getTransactions()
         ->_getCategories()
         ->_getPatterns()
         ->_categorize();
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
      $category->patterns = $category->getPatterns();
    }
    return $this;
  }
  protected function _categorize(){
    foreach($this->_categories as $category){
      $this->matches[$category->category] = 0;
      foreach($category->patterns as $pattern){
        foreach($this->_transactions as $transaction){
          if($this->_isMatch('/' . $pattern->pattern . '/',$transaction->memo)){
            $this->matches[$category->category]++;
          }
        }
      }
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
