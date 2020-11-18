<?php

use PHPUnit\Framework\TestCase;
use Operation\billpayment;

require_once __DIR__.'./../../src/billpayment/billpayment.php';

final class BillPaymentFailureTest extends TestCase {

    public function stubAccountDetail( $accNo, $billType, $expect ) {
        $stub = $this->getMockBuilder( billpayment::class )
        ->setConstructorArgs( ['acctNo'=>$accNo] )
        ->setMethods( array( 'getAccountDetail', 'saveChargeTransaction', 'saveTransaction' ) )
        ->getMock();

        $stub->method( 'getAccountDetail' )
        ->willReturn(
            array( 'isError' => true, 'message' => $expect ) );

            $stub->method( 'saveChargeTransaction' )
            ->willReturn( false );

            $stub->method( 'saveTransaction' )
            ->willReturn( false );

            return $stub;
        }

        public function testCanGetBillFailureStub( ) {
            $accNo = '12345';

            $stub = $this->stubAccountDetail( $accNo, 'waterCharge', 'Invalid Account no.' );

            $result = $stub->getBill( $accNo );

            $this->assertEquals( 'Invalid Account no.', $result['message'] );
        }

        public function testCanGetBillFailureStub2( ) {
            $accNo = '12345678901';

            $stub = $this->stubAccountDetail( $accNo, 'waterCharge', 'Invalid Account no.' );

            $result = $stub->getBill( $accNo );

            $this->assertEquals( 'Invalid Account no.', $result['message'] );
        }

        public function testCanPayFailureStub() {
            $accNo = '1234567890';
            $stub = $this->stubAccountDetail( $accNo, '', 'Invalid Account no.' );

            $result = $stub->pay( '' );

            $this->assertEquals( 'Invalid bill type', $result['message'] );
        }


        public function testCanPayFailureStub2() {
            $accNo = '1231231230';
            $stub = $this->stubAccountDetail( $accNo, 'waterCharge', 'ยอดเงินในบัญชีไม่เพียงพอ' );

            $result = $stub->pay( 'waterCharge' );

            $this->assertEquals( 'ยอดเงินในบัญชีไม่เพียงพอ', $result['message'] );
        }

        public function testCanGetBillFailureReal() {
            $accNo = '12345';

            $data = new billpayment( $accNo );
            $result = $data->getBill( $accNo );

            $this->assertEquals( 'Invalid Account no.', $result['message']);
        }

        public function testCanGetBillFailureReal2() {
            $accNo = '12345678901';

            $data = new billpayment( $accNo );
            $result = $data->getBill( $accNo );

            $this->assertEquals( 'Invalid Account no.', $result['message']);
        }

        public function testCanPayFailureReal() {
            $accNo = '1234567890';

            $data = new billpayment( $accNo );
            $result = $data->pay( '' );

            $this->assertEquals( 'Invalid bill type', $result['message'] );
        }

        public function testCanPayFailureReal2() {
            $accNo = '1231231230';

            $data = new billpayment( $accNo );
            $result = $data->pay( 'waterCharge' );

            $this->assertEquals( 'ยอดเงินในบัญชีไม่เพียงพอ', $result['message'] );
        }

    }
