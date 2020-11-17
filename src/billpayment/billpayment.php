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
        $this->accNo = $accNo;
    }

    public function getAccountDetail( string $accNo ) : array {
        if ( strlen( $this->accNo ) != 10 ) {
            $response['message'] = 'Invalid Account no.';
            $response['isError'] = true ;
            return $response;
        }
        return ServiceAuthentication::accountAuthenticationProvider( $accNo );
    }

    public function saveTransaction( string $accNo, string $updatedBalance ) : bool {
        return DBConnection::saveTransaction( $accNo, $updatedBalance );
    }

    public function saveChargeTransaction( string $accNo, string $bill_type ) : bool {
        if ( $bill_type == 'waterCharge' ) {
            return DBConnection::saveTransactionWaterCharge( $accNo, 0 );
        } else  if ( $bill_type == 'electricCharge' ) {
            return DBConnection::saveTransactionElectricCharge( $accNo, 0 );
        } else {
            return DBConnection::saveTransactionPhoneCharge( $accNo, 0 );
        }
    }

    public function getBill( string $accNo ) {

        try {
            $arrayAccount = $this->getAccountDetail( $this->accNo );
            $response['accNo'] = $arrayAccount['accNo'];
            $response['accName'] = $arrayAccount['accName'];
            $response['accBalance'] = $arrayAccount['accBalance'];
            $response['accWaterCharge'] = $arrayAccount['accWaterCharge'];
            $response['accElectricCharge'] = $arrayAccount['accElectricCharge'];
            $response['accPhoneCharge'] = $arrayAccount['accPhoneCharge'];
            $response['isError'] = false;
            $response['message'] = '';
        } catch( Error $e ) {
            $response['message'] = 'Cannot get bill';
            $response['isError'] = true;
        }

        return $response;

    }

    public function pay( string $bill_type ) {
        if ( $bill_type == null || $bill_type == '' ) {
            $response['isError'] = true;
            $response['message'] = 'Invalid bill type';
            return $response;
        } else {
            $arrayAccount = $this->getAccountDetail( $this->accNo );
            $accChargeType = '';

            if ( $bill_type == 'waterCharge' ) {
                $accChargeType = 'accWaterCharge';
            } else if ( $bill_type == 'electricCharge' ) {
                $accChargeType = 'accElectricCharge';
            } else {
                $accChargeType = 'accPhoneCharge';
            }

            if ( ( $arrayAccount['accBalance'] < $arrayAccount[$accChargeType] ) ) {
                $response['isError'] = true;
                $response['message'] = 'ยอดเงินในบัญชีไม่เพียงพอ';
                return $response;
            }
            if ( $arrayAccount['accBalance'] >= $arrayAccount[$accChargeType] ) {
                $updatedBalance = $arrayAccount['accBalance'] - $arrayAccount[$accChargeType];

                try {
                    $this->saveTransaction( $this->accNo, $updatedBalance );
                    $this->saveChargeTransaction( $this->accNo, $bill_type );

                    // $arrayAccount = $this->getAccountDetail( $this->accNo );
                    $response = $this->getAccountDetail( $this->accNo );
                    $response['isError'] = false;
                    $response['message'] = '';
                } catch( Error $e ) {
                    $response['isError'] = true;
                    $response['message'] = 'Unknown error occurs in BillPayment';
                }
            }

            return $response;
        }
    }
}

