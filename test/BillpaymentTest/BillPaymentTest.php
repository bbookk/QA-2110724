<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentSuccessTest extends TestCase {

    public function stubAccountDetail( $accNo, $billType, $expect ) {
        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['accNo'=>$accNo] )
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

        public function stubAccountDetailFailure( $accNo, $billType, $expect ) {
            $stub = $this->getMockBuilder( billpayment::class )
            ->setConstructorArgs( ['accNo'=>$accNo] )
            ->setMethods( array( 'getAccountDetail', 'saveChargeTransaction', 'saveTransaction' ) )
            ->getMock();

            $stub->method( 'saveChargeTransaction' )
            ->willReturn( false );

            $stub->method( 'saveTransaction' )
            ->willReturn( false );

            return $stub;
        }

        //STUB
        //TC_MB_01
        
        /**
        * @dataProvider billPaymentProvider
        */

        public function testTC_MB_01( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetail( $accNo, $billType, $expect );

            $result = $stub->getBill( $billType );

            $this->assertEquals( $expect['accNo'], $result['accNo']);

            $resultBill = $stub->pay( $billType );

            $this->assertEquals( $expect['isError'], $resultBill['isError'] );
        }

        /**
        * @dataProvider billPaymentFailureProvider
        */

        //TC_MB_02 and 03

        public function testTC_MB_02( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetailFailure( $accNo, $billType, $expect );

            $stub->method( 'getAccountDetail' )
            ->willReturn( 'ERROR' );

            $result = $stub->getBill( $billType );

            $this->assertEquals( $expect, $result);

            $resultBill = $stub->pay( $billType );

            $this->assertEquals( $expect['message'], $resultBill['message'] );
        }

        /**
        * @dataProvider billPaymentNoFlowProvider
        */

        //TC_MB_04 and 06

        public function testTC_MB_04( $accNo, $billType, $expect ) {

            $stub = $this->stubAccountDetailFailure( $accNo, $billType, $expect );

            $stub->method( 'getAccountDetail' )
            ->willReturn( $expect );

            $result = $stub->getBill( $billType );

            $this->assertEquals( $expect['accNo'], $result['accNo']);

            $resultBill = $stub->pay( $billType );

            $this->assertEquals( $expect['message'], $resultBill['message'] );
        }

        //REAL
        //TC_MB_01

        /**
        * @dataProvider billPaymentProvider
        */

         public function testRealTC_MB_01( $accNo, $billType, $expect ) {

            $real = new billpayment( $accNo );

            $result = $real->getBill( $billType );

            $this->assertEquals( $expect['accNo'], $result['accNo'] );

            $resultBill = $real->pay( $billType );

            $this->assertEquals( $expect['isError'], $resultBill['isError'] );
        }

        /**
        * @dataProvider billPaymentFailureProvider
        */

        //TC_MB_02 and 03

        public function testRealTC_MB_02( $accNo, $billType, $expect ) {

            $real = new billpayment( $accNo );

            $result = $real->getBill( $billType );

            $this->assertEquals( $expect, $result );

            $resultBill = $real->pay( '$billType' );

            $this->assertEquals( $expect['message'], $resultBill['message'] );
        }

          /**
        * @dataProvider billPaymentNoFlowProvider
        */

        //TC_MB_04 and 06

        public function testRealTC_MB_04( $accNo, $billType, $expect ) {

            $real = new billpayment( $accNo );

            $result = $real->getBill( $billType );

            $this->assertEquals( $expect['accNo'], $result['accNo'] );

            $resultBill = $real->pay( $billType );

            $this->assertEquals( $expect['message'], $resultBill['message'] );
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
                'accBalance' => '55555',
                'accWaterCharge' => '100',
                'accElectricCharge' => '300',
                'accPhoneCharge' => '800',
                'accName' => 'maytawee',
                'isError' => false,
                'message' => '' )]];
            }

            public function billPaymentFailureProvider() {
                return [
                    ['12345678901', 'waterCharge',
                    array( 'isError' => true,
                    'message' => 'Invalid Account No' )],
                    ['1235', 'electricCharge',
                    array( 'isError' => true,
                    'message' => 'Invalid Account No' )]];
                }

                public function billPaymentNoFlowProvider() {
                    return [
                        ['1231231230', 'waterCharge',
                        array( 'accNo' => '1231231230',
                        'accBalance' => '5000',
                        'accWaterCharge' => '10000',
                        'accElectricCharge' => '10000',
                        'accPhoneCharge' => '10000',
                        'accName' => 'narongtham',
                        'isError' => true,
                        'message' => 'ยอดเงินในบัญชีไม่เพียงพอ' )],
                        ['2222222222', '',
                        array( 'accNo' => '2222222222',
                        'accBalance' => '55555',
                        'accWaterCharge' => '100',
                        'accElectricCharge' => '300',
                        'accPhoneCharge' => '800',
                        'accName' => 'maytawee',
                        'isError' => true,
                        'message' => 'Invalid bill type' )]];
                    }
                }
