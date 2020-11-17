<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentFailureTest extends TestCase {

    /**
    * add DataProvider
    *
    * @dataProvider billPaymentProvider
    *
    */

    public function testCanGetBill( $accNo, $billType, $expect ) {

        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['acctNo'=>$accNo] )
        ->setMethods( array( 'getAccountDetail' ) )
        ->getMock();

        $stub->method( 'getAccountDetail' )
        ->willReturn(
            array( 'isError' => $expect['isError'], 'message' => $expect['message'] ) );

            $result = $stub->getBill( $accNo );
            $this->assertEquals( $expect, $result );
        }

        // public function testCanGetCorrecPayment( $accNo, $billType, $expect ) {
        //     $result = new billpayment();
        //     $this->assertEquals( null, $result->pay( $billType ) );
        // }

        public function billPaymentProvider() {
            return [['12345', 'waterCharge',
            array( 
            'isError' => true,
            'message' => 'Invalid Account no.' )]
            ];
        }

    }
