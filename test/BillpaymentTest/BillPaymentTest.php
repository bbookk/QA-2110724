<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentSuccessTest extends TestCase {

    public function stubAccountDetail( $accNo, $billType, $expect ) {
        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['acctNo'=>$accNo] )
        ->setMethods( array( 'getAccountDetail', 'saveChargeTransaction', 'saveTransaction' ) )
        ->getMock();

        $stub->method( 'getAccountDetail' )
        ->willReturn(
            array( 'accNo' => $expect['accNo'],
            'accBalance' => $expect['accBalance'], 'accName' => $expect['accName'],
            'accWaterCharge' => $expect['accWaterCharge'],
            'accElectricCharge' => $expect['accElectricCharge'],
            'accPhoneCharge' => $expect['accPhoneCharge']
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

        public function testCanGetBillStub( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

            $result = $stub->getBill( $accNo );

            $this->assertEquals( $expect, $result );
        }

        /**
        * @dataProvider billPaymentProvider
        */

        public function testCanPayStub( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

            $result = $stub->pay( $billType );

            $this->assertEquals( $expect['isError'], $result['isError'] );
        }

        /**
        * @dataProvider billPaymentProvider
        */

        public function testCanGetBillReal( $accNo, $billType, $expect ) {

            $data = new billpayment( $accNo );
            $result = $data->getBill( $accNo );

            $this->assertEquals( $expect['isError'], $result['isError'] );
        }

        /**
        * @dataProvider billPaymentProvider
        */

        public function testCanPayReal( $accNo, $billType, $expect ) {

            $data = new billpayment( $accNo );
            $result = $data->pay( $billType );

            $this->assertEquals( $expect['isError'], $result['isError'] );
        }

        public function stubAccountDetailFailure() {
            $stub = $this->getMockBuilder( billpayment::class )
            ->setConstructorArgs( ['acctNo'=>'1231231230'] )
            ->setMethods( array( 'getAccountDetail', 'saveChargeTransaction', 'saveTransaction' ) )
            ->getMock();

            $stub->method( 'getAccountDetail' )
            ->willReturn(
                array( 'accNo' => '1231231230',
                'accBalance' => 5000, 'accName' => 'narongtham',
                'accWaterCharge' => 10000,
                'accElectricCharge' => 10000,
                'accPhoneCharge' => 10000
                , 'isError' => true, 'message' => 'ยอดเงินในบัญชีไม่เพียงพอ' ) );

                $stub->method( 'saveChargeTransaction' )
                ->willReturn( false );

                $stub->method( 'saveTransaction' )
                ->willReturn( false );

                return $stub;
            }

            public function testCanPayFailureStub() {
                $stub = $this->stubAccountDetailFailure();
                $result =  $stub->pay( 'waterCharge' );

                $this->assertEquals( 'ยอดเงินในบัญชีไม่เพียงพอ', $result['message'] );
            }

            public function testCanPayFailureReal() {
                $accNo = '1231231230';

                $data = new billpayment( $accNo );
                $result = $data->pay( 'waterCharge' );

                $this->assertEquals( 'ยอดเงินในบัญชีไม่เพียงพอ', $result['message'] );
            }

            public function billPaymentProvider() {
                return [
                    ['1234567890', 'waterCharge',
                    array( 'accNo' => '1234567890',
                    'accBalance' => 2000,
                    'accWaterCharge' => 1000,
                    'accElectricCharge' => 2000,
                    'accPhoneCharge' => 3000,
                    'accName' => 'Wirot',
                    'isError' => false,
                    'message' => '' )],
                    ['6161616161', 'electricCharge',
                    array( 'accNo' => '6161616161',
                    'accBalance' => 2500,
                    'accWaterCharge' => 654,
                    'accElectricCharge' => 123,
                    'accPhoneCharge' => 5000,
                    'accName' => 'supachai',
                    'isError' => false,
                    'message' => '' )],
                    ['2222222222', 'phoneCharge',
                    array( 'accNo' => '2222222222',
                    'accBalance' => 55555,
                    'accWaterCharge' => 100,
                    'accElectricCharge' => 300,
                    'accPhoneCharge' => 800,
                    'accName' => 'maytawee',
                    'isError' => false,
                    'message' => '' )]];
                }

            }
