<?php namespace Operation;

use AccountInformationException;
use ServiceAuthentication;
use DBConnection;

require_once __DIR__.'./../serviceauthentication/serviceauthentication.php';
require_once __DIR__.'./../serviceauthentication/AccountInformationException.php';
require_once __DIR__.'./../serviceauthentication/DBConnection.php';

class billpayment {

    private $accNo = '';

    public function __construct( string $accNo ) {
        $this->$accNo = $accNo;
    }

    public function getAccountDetail( string $accNo ) : array {
        return ServiceAuthentication::accountAuthenticationProvider( $accNo );
    }

    public function saveTransaction( string $accNo ) : array {
        return DBConnection::saveTransaction( $accNo, $updatedBalance );
    }

    public function saveChargeTransaction( string $accNo, string $bill_type ) : array {
        if ( $bill_type == 'waterCharge' ) {
            return DBConnection::saveTransactionWaterCharge( $accNo, 0 );
        } else  if ( $bill_type == 'electricCharge' ) {
            return DBConnection::saveTransactionElectricCharge( $accNo, 0 );
        } else {
            return DBConnection::saveTransactionPhoneCharge( $accNo, 0 );
        }
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
        if ( $bill_type == null || $bill_type == '' ) {
            $response['isError'] = true;
            $response['message'] = 'Invalid bill type';
            return $response;
        } else {
            $arrayAccount = $this->getBill( $this->$accNo );

            if ( ( $arrayAccount['accBalance'] < $arrayAccount['accWaterCharge'] ) ||
            ( $arrayAccount['accBalance'] < $arrayAccount['accElectricCharge'] ) ||
            ( $arrayAccount['accBalance'] < $arrayAccount['accPhoneCharge'] ) ) {
                $response['isError'] = true;
                $response['message'] = 'ยอดเงินในบัญชีไม่เพียงพอ';
                return $response;
            }
            if ( $bill_type == 'waterCharge' ) {
                if ( $arrayAccount['accBalance'] >= $arrayAccount['accWaterCharge'] ) {
                    $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accWaterCharge'];

                    try {
                        $this->saveTransaction( $this->$accNo, $updatedBalance );
                        $this->saveChargeTransaction( $this->$accNo, $bill_type );

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
            }else if ( $bill_type == 'electricCharge' ) {
                if ( $arrayAccount['accBalance'] >= $arrayAccount['accElectricCharge'] ) {
                    $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accElectricCharge'];

                    try {
                        $this->saveTransaction( $this->$accNo, $updatedBalance );
                        $this->saveChargeTransaction( $this->$accNo, $bill_type );

                        $response = ServiceAuthentication::accountAuthenticationProvider( $this->$accNo );
                        $response['isError'] = false;
                        $response['message'] = '';
                    } catch( Error $e ) {
                        $response['message'] = 'Unknown error occurs in BillPayment';
                    }
                }
            }else if ( $bill_type == 'phoneCharge' ) {
                if ( $arrayAccount['accBalance'] >= $arrayAccount['accPhoneCharge'] ) {
                    $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount['accPhoneCharge'];

                    try {
                        $this->saveTransaction( $this->$accNo, $updatedBalance );
                        $this->saveChargeTransaction( $this->$accNo, $bill_type );

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
}

