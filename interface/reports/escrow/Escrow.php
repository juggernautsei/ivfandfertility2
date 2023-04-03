<?php
/*
 *  @package   OpenEMR
 *  @link      http://www.open-emr.org
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 */

namespace OpenEMR\Escrow;

    class Escrow
    {
        public int $arSessionId;
        public function retrieveAllEscrowPayments(): array
        {
            $esql = "SELECT `session_id`, `check_date`, `pay_total`, `payment_method` FROM `ar_session` WHERE `patient_id` = ?" .
                " AND adjustment_code = ?";
            $payments = sqlStatement($esql, [$_SESSION['pid'], 'pre_payment']);
            $paymentData = [];
            while ($escrowamounts = sqlFetchArray($payments))
            {
                $paymentData[] = $escrowamounts;
            }
            return $paymentData;
        }

        public function getEncounterPayments()
        {
            $encPayments = [];
            $sql = "SELECT * FROM `ar_activity` WHERE
                                `session_id` = ? AND account_code = 'PP' AND deleted IS NULL;";
            $fetchPayments = sqlStatement($sql, [$this->arSessionId]);
            while ($entries = sqlFetchArray($fetchPayments)) {
                $encPayments[] = $entries;
            }
            return $encPayments;
        }

    }
