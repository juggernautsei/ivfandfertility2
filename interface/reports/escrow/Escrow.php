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
        public int $encounter;
        public string $checkDate;
        public string $payTotal;
        public string $reference;

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

        public function getRefund()
        {
            $rsql = "SELECT session_id, reference, check_date, pay_total FROM `ar_session` WHERE patient_id = ? AND adjustment_code = 'refund_balance'";
            return sqlQuery($rsql, [$_SESSION['pid']]);
        }

        public function enterRefundedAmount(): string
        {

            $sql = "INSERT INTO `ar_session` (`session_id`,
                          `payer_id`,
                          `user_id`,
                          `closed`,
                          `reference`,
                          `check_date`,
                          `deposit_date`,
                          `pay_total`,
                          `created_time`,
                          `modified_time`,
                          `global_amount`,
                          `payment_type`,
                          `description`,
                          `adjustment_code`,
                          `post_to_date`,
                          `patient_id`,
                          `payment_method`
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), ?, ?, ?, ?, NOW(), ?, ?, ?)";

            sqlStatement($sql,
                [
                    NULL,
                    0,
                    $_SESSION['authUserID'],
                    0,
                    $this->reference,
                    $this->checkDate,
                    $this->checkDate,
                    $this->payTotal,
                    '0.00',
                    'clinic',
                    'Balance Refund',
                    'refund_balance',
                    $_SESSION['pid'],
                    'check_payment'
                ]);

             return 'success';
        }

        public function getEncounterPayments(): array
        {
            $encPayments = [];
            $sql = "SELECT ar.encounter, fe.date, SUM(ar.pay_amount) AS pay_amount FROM `ar_activity` AS ar
                    LEFT JOIN `form_encounter` fe ON fe.encounter = ar.encounter
                    WHERE ar.encounter = ? AND
                    ar.session_id = ? AND ar.account_code = 'PP' AND ar.deleted IS NULL";
            $fetchPayments = sqlStatement($sql, [$this->encounter, $this->arSessionId]);
            while ($entries = sqlFetchArray($fetchPayments)) {
                $encPayments[] = $entries;
            }
            return $encPayments;
        }

        public function getEncounterEntries()
        {
            $enc = [];
            $sql = "SELECT `encounter` FROM `ar_activity` WHERE `session_id` = ? AND `account_code` = 'PP' AND `deleted` IS NULL GROUP BY `encounter`";
            $listEnc = sqlStatement($sql, [$this->arSessionId]);
            while ($row = sqlFetchArray($listEnc)) {
                $enc[] = $row;
            }
            return $enc;
        }

    }
