<?php

require_once __DIR__ . '/../Models/Transaction.php';

class AutoCategorizer{

  const JETS = '/JETS\sPIZZA/';
  const BK = '/BURGER\sKING/';
  const POPEYE = '/POPEYES/';
  const WENDY = '/WENDYS/';
  const TACO = '/TACO\sBELL/';
  const SNS = '/STEAK\sN\sSHAKE/';
  const ARBY = '/ARBYS/';
  const WINGSTOP = '/WINGSTOP/';
  const TOKYOGRILL = '/TOKYO\sGRILL/';
  const SUBWAY =  '/SUBWAY/';
  const SONIC = '/SONIC\sDRIVE\sIN/';
  const MC = '/MCDONALDS/';
  const PAPA = '/PAPA\sJOHNS/';
  const LILCAES = '/LITTLE\sCAESARS/';
  const WHISKER = '/WHISKERS\sWINE\s&\sSPIRIT/';
  const SASHA = '/SASHA\sWINE\s&\sLIQUOR/';
  const ATT = '/ATT\*BILL\sPAYMENT/';
  const TMOB1 = '/T\sMOBILE\sCOM/';
  const TMOB2 = '/TMOBILE\*AUTO\sPAY/';
  const MLGW = '/MLGW\s\*UTILITY/';
  const MLGW2 = '/MLGW/';
  const STATEFARM = '/STATE\sFARM,/';
  const VA = '/VADMC/';
  const TRINLAKES = '/CLKPROPERTIES586/';
  const DEXRESERVE = '/Reserve\sAt\sDexte/';
  const STORAGE = '/EXTRA\sSPACE\sSTOR/';
  const CIVIC = '/HONDA\sPMT/';

  public static $knownDining = array(
    self::JETS,
    self::BK,
    self::POPEYE,
    self::WENDY,
    self::TACO,
    self::SNS,
    self::ARBY,
    self::WINGSTOP,
    self::TOKYOGRILL,
    self::SUBWAY,
    self::SONIC,
    self::MC,
    self::PAPA,
    self::LILCAES
  );
  public static $knownAlcohol = array(
    self::WHISKER,
    self::SASHA
  );
  public static $knownPhones = array(
    self::TMOB1,
    self::TMOB2
  );
  public static $knownUtilities = array(
    self::MLGW,
    self::MLGW2
  );
  public static $knownRents = array(
    self::TRINLAKES,
    self::DEXRESERVE
  );

  public function __construct(){
    $this->_getTransactions()->_categorize();
  }
  protected function _getTransactions(){
    $transactions = Record::search(Transaction::DB,Transaction::TABLE,Transaction::PRIMARYKEY,'category',null);
    print_r($transactions);
    return $this;
  }
  protected function _categorize(){
    foreach($this->_transactions as $transaction){
      //todo categorize
    }
    return $this;
  }

}
