<?php

require_once __DIR__ . '/../Libs/Imap/Imap.php';
require_once __DIR__ . '/../Models/Transaction.php';

class PayPalReceipt extends Imap{

  const AMNTPATT = '/([0-9]{1,5}\.[0-9]{2})\sUSD/';
  const MSGRECPATT = '/Date:\s(.*)/';
  const FROMADD = 'service@paypal.com';
  const SUBJPATT = '/Receipt\sfor\sYour\sPayment\sto/';

  public function __construct($host,$user,$pass,$port){
    parent::__construct($host,$user,$pass,$port);
    $this->_parse();
  }
  protected function _parse(){
    $results = $this->search('FROM',self::FROMADD);
    foreach($results as $result){
      if(preg_match(self::SUBJPATT,$result[0]->subject)){
        $head = $this->getMsg($result[0]->msgno,0);
        $body = $this->getMsg($result[0]->msgno,1);
        $html = base64_decode($body);
        try{
          $receivedDate = $this->_parseMsgReceived($head);
          $dollars = $this->_parseDollarAmnt($html);
        }catch(\Exception $e){
          echo $e->getMessage() . "\m";
        }
        while(!$transaction = $this->_getTransaction($dollars,$receivedDate)){
          $receivedDate = $this->_iterateDate($receivedDate);
        }
        print_r($transaction);
      }
    }
    return $this;
  }
  protected function _parseDollarAmnt($htmlStr){
    if(!preg_match_all(self::AMNTPATT,$htmlStr,$matches)){
      throw new \Exception('Unable to match dollar amount.');
    }
    return $matches[1][0];
  }
  protected function _parseMsgReceived($str){
    if(!preg_match_all(self::MSGRECPATT,$str,$matches)){
      throw new \Exception('Unable to match received date.');
    }
    return date('Y-m-d',strtotime($matches[1][0]));
  }
  protected function _iterateDate($dateStr,$numDays = 1){
    $date = date_add(date_create($dateStr),date_interval_create_from_date_string($numDays . ' days'));
    return date_format($date,"Y-m-d"); //H:i:s
  }
  protected function _getTransaction($amnt,$date){
    $obj = null;
    $amnt = -1 * abs($amnt);
    $results = $GLOBALS['db']
      ->database(Transaction::DB)
      ->table(Transaction::TABLE)
      ->select(Transaction::PRIMARYKEY)
      ->where("memo","like","'%paypal%'")
      ->andWhere("amount","=",$amnt)
      ->andWhere("date","=","'" . $date . "'")
      ->get();
    if(!mysqli_num_rows($results)){
      return false;
    }
    while($row = mysqli_fetch_assoc($results)){
      $obj = new Transaction($row[Transaction::PRIMARYKEY]);
    }
    return $obj;
  }
}

$host = 'imap.gmail.com';
$user = 'outlawstar4761@gmail.com';
$pass = 'B00TSw34t';
$port = 993;
$proc = new PayPalReceipt($host,$user,$pass,$port);
