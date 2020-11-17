<?php namespace Operation;

use DBConnection;
use ServiceAuthentication;

class billpayment {
    private $accNo;

    public function __construct( string $accNo ) {
        $this->$accNo = $accNo;
    }

    public function getAccountDetail( string $accNo ) : array {
        return ServiceAuthentication::accountAuthenticationProvider( $accNo );
    }

    public function saveTransaction( string $accNo ) : array {
        return DBConnection::saveTransaction( $accNo, $updatedBalance );
    }

    public function saveChargeTransaction( string $accNo ) : array {
        return DBConnection::saveTransactionWaterCharge( $accNo, 0 );
    }

    public function getBill( string $accNo ) {
        if ( strlen( $this->$accNo ) != 10 ) {
            $response['message'] = 'Invalid Account no.';
            $response['isError'] = true ;
            return $response;
        } else {
            try {
                $arrayAccount = $this->getAccountDetail( $this->$accNo );
                $response['accNo'] = $arrayAccount['accNo'];
                $response['accName'] = $arrayAccount['accName'];
                $response['accBalance'] = $arrayAccount['accBalance'];
                $response['isError'] = false;
                $response['message'] = '';
            } catch( Error $e ) {
                $response['message'] = 'Cannot get bill';
                $response['isError'] = true;
            }

            return $response;
        }
    }

    public function pay( string $bill_type ) {
        $arrayAccount = $this->getAccountDetail( $this->$accNo );
        $response['isError'] = true;
        $response['message'] = 'ยอดเงินในบัญชีไม่เพียงพอ';

        if ( $bill_type == 'waterCharge' ) {
            if ( $arrayAccount['accBalance'] >= $arrayAccount['accWaterCharge'] ) {
                $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accWaterCharge'];

                try {
                    $this->saveTransaction( $this->$accNo, $updatedBalance );
                    $this->saveTransaction( $this->$accNo, $updatedBalance );

                    // $arrayAccount = $this->getAccountDetail( $this->$accNo );
                    $response['accNo'] = $arrayAccount['accNo'];
                    $response['accName'] = $arrayAccount['accName'];
                    $response['accBalance'] = $arrayAccount['accBalance'];
                    $response['isError'] = false;
                    $response['message'] = '';
                } catch( Error $e ) {
                    $response['message'] = 'Unknown error occurs in BillPayment';
                }
            }
        }

        if ( $bill_type == 'electricCharge' ) {
            if ( $arrayAccount['accBalance'] >= $arrayAccount['accElectricCharge'] ) {
                $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accElectricCharge'];

                try {
                    DBConnection::saveTransaction( $this->$accNo, $updatedBalance );
                    DBConnection::saveChargeTransaction( $this->$accNo, 0 );

                    $response = ServiceAuthentication::accountAuthenticationProvider( $this->$accNo );
                    $response['isError'] = false;
                    $response['message'] = '';
                } catch( Error $e ) {
                    $response['message'] = 'Unknown error occurs in BillPayment';
                }
            }
        }

        if ( $bill_type == 'phoneCharge' ) {
            if ( $arrayAccount['accBalance'] >= $arrayAccount['accPhoneCharge'] ) {
                $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accPhoneCharge'];

                try {
                    DBConnection::saveTransaction( $this->$accNo, $updatedBalance );
                    DBConnection::saveTransactionPhoneCharge( $this->$accNo, 0 );

                    $response = ServiceAuthentication::accountAuthenticationProvider( $this->$accNo );
                    $response['isError'] = false;
                    $response['message'] = '';
                } catch( Error $e ) {
                    $response['message'] = 'Unknown error occurs in BillPayment';
                }
            }
        }

        return $response;
    }
}

