<?php

include_once 'ServiceType.php';
include_once 'AccountInformationException.php';
include_once 'BillingException.php';

class DBConnection {

    public static function accountInformationProvider(): array {
        $argument = func_get_args();

        if (count($argument) == 1) {
            return DBConnection::serviceAuthentication($argument[0]);
        }
        elseif(count($argument) == 2) {
            return DBConnection::userAuthentication(
                $argument[0],
                $argument[1]
            );
        }
    }

    public static function saveTransaction(string $accNo, int $updatedBalance): bool {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "UPDATE ACCOUNT SET balance = ". $updatedBalance. " WHERE no = ". $accNo;
        $result = $con->query($stmt);
        $con->close();

        return $result;
    }

    private static function serviceAuthentication(string $accNo): array {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "SELECT no as accNo, "
            . "name as accName, "
            . "balance as accBalance, "
            . "waterCharge as accWaterCharge, "
            . "electricCharge as accElectricCharge, "
            . "phoneCharge as accPhoneCharge "
            . "FROM ACCOUNT "
            . "WHERE no = ". $accNo;
        $result = $con->query($stmt);
        $con->close();

        if ($result->num_rows == 0) {
            throw new AccountInformationException("Account number : {$accNo} not found.");
        }
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    private static function userAuthentication(string $accNo, string $pin): array {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "SELECT no as accNo, "
            . "name as accName, "
            . "balance as accBalance "
            . "FROM ACCOUNT "
            . "WHERE no = ". $accNo. " AND pin = ". $pin;
        $result = $con->query($stmt);
        $con->close();

        if ($result->num_rows == 0) {
            throw new AccountInformationException("Account number or PIN is invalid.");
        }
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public static function saveTransactionWaterCharge(string $accNo, int $updatedBalance): bool {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "UPDATE ACCOUNT SET waterCharge = ". $updatedBalance. " WHERE no = ". $accNo;
        $result = $con->query($stmt);
        $con->close();

        return $result;
    }

    public static function saveTransactionElectricCharge(string $accNo, int $updatedBalance): bool {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "UPDATE ACCOUNT SET electricCharge = ". $updatedBalance. " WHERE no = ". $accNo;
        $result = $con->query($stmt);
        $con->close();

        return $result;
    }

    public static function saveTransactionPhoneCharge(string $accNo, int $updatedBalance): bool {
        $con = new mysqli('127.0.0.1:8080', 'root', '', 'integration');

        $stmt = "UPDATE ACCOUNT SET phoneCharge = ". $updatedBalance. " WHERE no = ". $accNo;
        $result = $con->query($stmt);
        $con->close();

        return $result;
    }
}
