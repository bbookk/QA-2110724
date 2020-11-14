<?php namespace Operation;

use DBConnection;
use ServiceAuthentication;

class billpayment{
    private $acctInfo;

    public function __construct(string $session){
      $this->acctInfo = ServiceAuthentication::accountAuthenticationProvider($session);
    }

    public function getBill(string $bill_type){
      try{
        $response = ServiceAuthentication::accountAuthenticationProvider($this->acctInfo['accNo']);
        $response["isError"] = false;
        $response["message"] = "";
      }
      catch(Error $e){
        $response["message"] = "Unknown error occurs in BillPaymentInq";
        $response["isError"] = true;
      }
      return $response;
    }

    public function pay(string $bill_type){

      $response["isError"] = true;
      $response["message"] = "ยอดเงินในบัญชีไม่เพียงพอ";

      if($bill_type == 'waterCharge'){
        if($this->acctInfo['accBalance'] >= $this->acctInfo['accWaterCharge']) {
          $updatedBalance = $this->acctInfo['accBalance'] - $this->acctInfo['accWaterCharge'];

          try{
            DBConnection::saveTransaction($this->acctInfo['accNo'], $updatedBalance);
            DBConnection::saveTransactionWaterCharge($this->acctInfo['accNo'], 0);

            $response = ServiceAuthentication::accountAuthenticationProvider($this->acctInfo['accNo']);
            $response["isError"] = false;
            $response["message"] = "";
          }
          catch(Error $e){
            $response["message"] = "Unknown error occurs in BillPayment";
          }
        }
      }


      if($bill_type == 'electricCharge'){
        if($this->acctInfo['accBalance'] >= $this->acctInfo['accElectricCharge']) {
          $updatedBalance = $this->acctInfo['accBalance'] - $this->acctInfo['accElectricCharge'];

          try{
            DBConnection::saveTransaction($this->acctInfo['accNo'], $updatedBalance);
            DBConnection::saveTransactionElectricCharge($this->acctInfo['accNo'], 0);

            $response = ServiceAuthentication::accountAuthenticationProvider($this->acctInfo['accNo']);
            $response["isError"] = false;
            $response["message"] = "";
          }
          catch(Error $e){
            $response["message"] = "Unknown error occurs in BillPayment";
          }
        }
      }


      if($bill_type == 'phoneCharge'){
        if($this->acctInfo['accBalance'] >= $this->acctInfo['accPhoneCharge']) {
          $updatedBalance = $this->acctInfo['accBalance'] - $this->acctInfo['accPhoneCharge'];

          try{
            DBConnection::saveTransaction($this->acctInfo['accNo'], $updatedBalance);
            DBConnection::saveTransactionPhoneCharge($this->acctInfo['accNo'], 0);

            $response = ServiceAuthentication::accountAuthenticationProvider($this->acctInfo['accNo']);
            $response["isError"] = false;
            $response["message"] = "";
          }
          catch(Error $e){
            $response["message"] = "Unknown error occurs in BillPayment";
          }
        }
      }

      return $response;
    }
}


