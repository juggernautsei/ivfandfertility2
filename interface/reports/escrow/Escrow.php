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
        public function retrieveAllEscrowPayments()
        {
            $esql = "SELECT `session_id`, `check_date`, `pay_total` FROM `ar_session` WHERE `patient_id` = ?";
            $payments = sqlStatement($esql, [$_SESSION['pid']]);
            $paymentData = [];
            while ($escrowamounts = sqlFetchArray($payments))
            {
                $paymentData[] = $escrowamounts;
            }
            return $paymentData;
        }

    }
