<?php namespace Stub;

class StubWithdrawal
{

    public static function serviceAuthentication($accNo): array
    {
        if ($accNo == '1234567890') {
            return array("accNo" => $accNo, "accName" => "TestAccountName", "accBalance" => 10000, "isError" => false);
        } else {
            return array("isError" => true, "message" => "Account number : " . $accNo . " not found.");
        }
    }

    public static function doWithdraw($accNo, $amount): array
    {
        if (10000 - $amount >= 0) {
            return array("accNo" => $accNo, "accName" => "TestAccountName", "accBalance" => 10000 - $amount, "isError" => false);
        }else {
            return array("isError" => true, "message" => "ยอดเงินในบัญชีไม่เพียงพอ");
        }
    }

    public static function saveTransaction($accNo, $updatedBalance): bool
    {
        return true;
    }
}
