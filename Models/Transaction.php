<?php

require_once __DIR__ . '/../Libs/Record/Record.php';
require_once __DIR__ . '/TransactionCategory.php';

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
  public static function recordExists($date,$amount,$memo){
    $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select(self::PRIMARYKEY)
      ->where('amount','=',"'" . $amount . "'")
      ->andWhere('date','=',"'" . $date . "'");
    if(is_null($memo)){
      $GLOBALS['db']->andWhere("memo","is","null");
    }else{
      $GLOBALS['db']->andWhere("memo","=","'" . $memo . "'");
    }
    $results = $GLOBALS['db']->orderBy(Transaction::PRIMARYKEY . " limit 1")->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    return true;
  }
  public static function getUncategorized(){
    $data = array();
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select(self::PRIMARYKEY)
      ->where("category","is","null")
      ->get();
    while($row = mysqli_fetch_assoc($results)){
      $data[] = new self($row[self::PRIMARYKEY]);
    }
    return $data;
  }
  public static function matchPayPalTransaction($amnt,$date){
    $obj = null;
    $amnt = -1 * abs($amnt);
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select(self::PRIMARYKEY)
      ->where("memo","like","'%paypal%'")
      ->andWhere("amount","=",$amnt)
      ->andWhere("date","=","'" . $date . "'")
      ->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    while($row = mysqli_fetch_assoc($results)){
      $obj = new self($row[self::PRIMARYKEY]);
    }
    return $obj;
  }
  public static function getYearlyUncategorizedTotals(){
    $data = array();
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE)
      ->select("count(*) as total_transactions, sum(amount) as total_spent, 'null' as category, year(date) as tyear")
      ->where('category','is','null')
      ->groupBy('category, year(date)')
      ->orderBy('category,tyear')
      ->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    while($row = mysqli_fetch_assoc($results)){
      $data[] = $row;
    }
    return $data;
  }
  public static function categoryBreakDown($expense = true){
    $data = array();
    $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE . ' transaction')
      ->select('count(transaction.id) as total_transactions,sum(transaction.amount) as total_spent,category.category,year(transaction.date) as tyear')
      ->join(TransactionCategory::TABLE . ' category','transaction.category','=','category.id');
    if($expense){
      $GLOBALS['db']->where('transaction.amount','<',0);
    }else{
      $GLOBALS['db']->where('transaction.amount','>',0);
    }
    $results = $GLOBALS['db']->groupBy('category.category, year(transaction.date)')->orderBy('category,tyear')->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('No expenses available');
    }
    while($row = mysqli_fetch_assoc($results)){
      $data[] = $row;
    }
    return $data;
  }
  public static function getExpensesByCategory(){
    $data = array();
    $results = $GLOBALS['db']
      ->database(self::DB)
      ->table(self::TABLE . ' transaction')
      ->select('count(transaction.id) as total_transactions,sum(transaction.amount) as total_spent,category.category,year(transaction.date) as tyear')
      ->join(TransactionCategory::TABLE . ' category','transaction.category','=','category.id')
      // ->where('transaction.amount','<',0)
      ->groupBy('category.category, year(transaction.date)')
      ->orderBy('category,tyear')
      ->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('No expenses available');
    }
    while($row = mysqli_fetch_assoc($results)){
      $data[] = $row;
    }
    return $data;
  }
  public static function getYearlyTotals($expense = true){
    $data = array();
    $GLOBALS['db']->database(self::DB)->table(self::TABLE);
    if($expense){
      $GLOBALS['db']->select("(sum(amount) * -1) as total,year(date) as year")->where('amount','<',0);
    }else{
      $GLOBALS['db']->select("sum(amount) as total,year(date) as year")->where('amount','>',0);
    }
    $results = $GLOBALS['db']->groupBy('year')->get();
    if(!mysqli_num_rows($results)){
      throw new \Exception('Unable to get data');
    }
    while($row = mysqli_fetch_assoc($results)){
      $data[$row['year']] = $row['total'];
    }
    return $data;
  }
}
