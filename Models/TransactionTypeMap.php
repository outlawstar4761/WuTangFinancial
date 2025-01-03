<?php

class TransactionTypeMap{
  const TYPES = array(
    "POS Withdrawal"=>array("POS Card purchase","Other"),
    "ATM Withdrawal"=>array("ATM Withdrawal","Other"),
    "ACH Withdrawal"=>array("ACH Withdrawal","Other"),
    "ACH Deposit"=>array("ACH Deposit","Other"),
    "Card purchase"=>array("Card Card purchase","Other"),
    "Dividend Deposit"=>array("Dividend Deposit","Dividend"),
    "ATM Deposit"=>array("ATM Deposit","Other"),
    "Cash Withdrawal"=>array("Cash Withdrawal","Cash Withdrawal"),
    "Check Deposit"=>array("Check Deposit","Other"),
    "Bill payment"=>array("Bill payment Card purchase","Other"),
    "Check"=>array("Draft Withdrawal","Check"),
    "Home banking"=>array("Withdrawal Transfer to Share 0000","Transfer"),
    "Premium Checking"=>array("Premium Checking Monthly Fee","Fee"),
    "Deposit ATM"=>array("Deposit","Other"),
    "Deposit Premium"=>array("Deposit","Dividend")
  );
}
