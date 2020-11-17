<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentSuccessTest extends TestCase {

    public function stubAccountDetail( $accNo, $billType, $expect ) {
        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['acctNo'=>'1234567890'] )
        ->setMethods( array( 'getAccountDetail', 'saveChargeTransaction', 'saveTransaction' ) )
        ->getMock();

        $stub->method( 'getAccountDetail' )
        ->willReturn(
            array( 'accNo' => $expect['accNo'],
            'accBalance' => $expect['accBalance'], 'accName' => $expect['accName']
            , 'isError' => $expect['isError'], 'message' => $expect['message'] ) );

            $stub->method( 'saveChargeTransaction' )
            ->willReturn( true );

            $stub->method( 'saveTransaction' )
            ->willReturn( true );

            return $stub;
        }

        /**
        * @dataProvider billPaymentProvider
        */

        // public function testCanGetBillStub( $accNo, $billType, $expect ) {

        //     $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

        //     $result = $stub->getBill( $accNo );

        //     $this->assertEquals( $expect, $result );
        // }

        /**
        * @dataProvider billPaymentProvider
        */

        // public function testCanPayStub( $accNo, $billType, $expect ) {

        //     $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

        //     $result = $stub->pay( $billType );

        //     $expected = array( 'isError'=>$expect['isError'], 'message'=>$expect['message'] );

        //     $this->assertEquals( $expected, $result );
        // }

        /**
        * @dataProvider billPaymentProvider
        */

        // public function testCanGetBillReal( $accNo, $billType, $expect ) {

        //     $data = new billpayment( $accNo );
        //     $result = $data->getBill( $accNo );

        //     $this->assertEquals( $expect, $result );
        // }

        /**
        * @dataProvider billPaymentProvider
        */

        // public function testCanPayReal( $accNo, $billType, $expect ) {

        //     $data = new billpayment( $accNo );
        //     $result = $data->pay( $billType );

        //     $expected = array( 'isError'=>$expect['isError'], 'message'=>$expect['message'] );

        //     $this->assertEquals( $expected, $result );
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
