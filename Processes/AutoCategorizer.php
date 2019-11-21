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

  protected $_transactions = array();

  public function __construct(){
    $this->_getTransactions()->_categorize();
  }
  protected function _getTransactions(){
    $this->_transactions = Transaction::getUncategorized();
    return $this;
  }
  protected function _categorize(){
    $alcohol = 0;
    $utility = 0;
    $internet = 0;
    $phone = 0;
    $insurance = 0;
    $va = 0;
    $rent = 0;
    $storage = 0;
    $car = 0;
    foreach($this->_transactions as $transaction){
      foreach($this->knownDining as $diningPattern){
        if($this->_isMatch($diningPattern,$transaction->memo)){
          $dinning++;
        }
      }
      foreach($this->knownAlcohol as $alcoholPattern){
        if($this->_isMatch($alcoholPattern,$transaction->memo)){
          $alcohol++;
        }
      }
      foreach($this->knownPhones as $phonePattern){
        if($this->_isMatch($phonePattern,$transaction->memo)){
          $phone++;
        }
      }
      foreach($this->knownUtilities as $utilityPattern){
        if($this->_isMatch($phonePattern,$transaction->memo)){
          $utility++;
        }
      }
      foreach($this->knownRents as $rentPattern){
        if($this->_isMatch($rentPattern,$transaction->memo)){
          $rent++;
        }
      }
      if($this->_isMatch(self::ATT,$transaction->memo)){
        $internet++;
      }
      if($this->_isMatch(self::STATEFARM,$transaction->memo)){
        $insurance++;
      }
      if($this->_isMatch(self::VA,$transaction->memo)){
        $va++;
      }
      if($this->_isMatch(self::STORAGE,$transaction->memo)){
        $storage++;
      }
      if($this->_isMatch(self::CIVIC,$transaction->memo)){
        $car++;
      }
    }
    echo count($this->transactions) . "\n";
    echo "Alcohol: " . $alcohol . "\n";
    echo "Dining: " . $dining . "\n";
    echo "Utility: " . $utility . "\n";
    echo "Internet: " . $internet . "\n";
    echo "Phone: " . $phone . "\n";
    echo "Insurance: " . $insurance . "\n";
    echo "VA: " . $va . "\n";
    echo "Rent: " . $rent . "\n";
    echo "Storage: " . $storage . "\n";
    echo "Car: " . $car . "\n";
    echo "____________\n";
    echo $alcohol + $dining + $utility + $internet + $phone + $insurance + $va + $storage + $rent + $car . "\n";
    return $this;
  }
  protected function _isMatch($pattern,$str){
    if(preg_match($pattern,$str)){
      return true;
    }
    return false;
  }

}
