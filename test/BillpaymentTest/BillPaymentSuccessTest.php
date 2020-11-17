<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentSuccessTest extends TestCase {
    public function stubAccountDetail( $accNo, $billType, $expect ) {
        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['acctNo'=>$accNo] )
        ->setMethods( array( 'getAccountDetail' ) )
        ->getMock();

        $stub->method( 'getAccountDetail' )
        ->willReturn(
            array( 'accNo' => $expect['accNo'],
            'accBalance' => $expect['accBalance'], 'accName' => $expect['accName']
            , 'isError' => $expect['isError'], 'message' => $expect['message'] ) );

            return $stub;
        }

        /**
        * add DataProvider
        *
        * @dataProvider billPaymentProvider
        *
        */

        public function testCanGetBill( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

            $result = $stub->getBill( $accNo );
            $this->assertEquals( $expect, $result );
        }

          /**
        * add DataProvider
        *
        * @dataProvider billPaymentProvider
        *
        */

        // public function testCanPay( $accNo, $billType, $expect ) {

        //     $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

        //     $result = $stub->pay( $billType );
        //     $this->assertEquals( $expect, $result );
        // }

        // public function testCanGetCorrecPayment( $accNo, $billType, $expect ) {
        //     $result = new billpayment();
        //     $this->assertEquals( null, $result->pay( $billType ) );
        // }

        public function billPaymentProvider() {
            return [['1234567890', 'waterCharge',
            array( 'accNo' => '1234567890',
            'accBalance' => 39000,
            'accName' => 'Test 1',
            'isError' => false,
            'message' => '' )],
            ['1234567890', 'electricCharge',
            array( 'accNo' => '1234567890',
            'accBalance' => 2000,
            'accName' => 'Test 1',
            'isError' => false,
            'message' => '' )],
            ['1234567890', 'phoneCharge',
            array( 'accNo' => '1234567890',
            'accBalance' => 2000,
            'accName' => 'Test 1',
            'isError' => false,
            'message' => '' )]];
        }

    }
