<?php

    /**
     *  package OpenEMR
     *  link    https://www.open-emr.org
     *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
     *  Copyright (c) 2022.
     *  All rights reserved
     */

    require_once dirname(__DIR__, 3) . "/../globals.php";
    require_once dirname(__DIR__) . "/vendor/autoload.php";

    use Juggernaut\App\Model\NotificationModel;
    use Juggernaut\App\Controllers\SendMessage;
function start_appt_reminders()
{
    $process = new NotificationModel();
    $personsToBeContacted = $process->getAppointments();
    error_log('Starting appt reminders ' . date('Y-m-d H:i:s') . ' ' . count($personsToBeContacted));
    if (empty($personsToBeContacted)) {
        $process->resetBackgroundService();
        die('nothing to process');
    }

    foreach ($personsToBeContacted as $person) {
        if ($person['phone_cell'] == '') {
            continue;
        }
        if ($person['hipaa_allowsms'] != 'YES') {
            continue;
        }
        $message = "
           Important Reminder:
            You have an
            appointment
            scheduled on " .
            $person['pc_eventDate'] . " at " .
            $person['pc_startTime'] . ".
              If you can not
             make this appointment
             please call
            Thank
            you.";
        $cellNumber = $process->stripDashesFromNumber($person['phone_cell']);
        $response = SendMessage::outBoundMessage((int)$cellNumber, $message);

        $sdate = date("Y-m-d H:i:s");
        $patient_info = '';
    $patient_info = $person['title'] ?? '' . " " . $person['fname'] . " " . $person['mname'] . " " . $person['lname'] .
        "|||" . $person['phone_cell'] . "|||" . $person['email'];
    $data_info = $person['pc_eventDate'] . "|||" . $person['pc_endDate'] . "|||" . $person['pc_startTime'] . "|||" .
        $person['pc_endTime'];
        $sdate = date("Y-m-d H:i:s");
    $sql_loginsert = "INSERT INTO `notification_log` (
                                `iLogId`,
                                `pid`,
                                `pc_eid`,
                                `sms_gateway_type`,
                                `message`,
                                `type`,
                                `patient_info`,
                                `smsgateway_info`,
                                `pc_eventDate`,
                                `pc_endDate`,
                                `pc_startTime`,
                                `pc_endTime`,
                                `dSentDateTime`
                                ) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?)";

        $safe = array($person['pid'], $person['pc_eid'], 'TEXTBELT', $message, 'SMS' || '', $patient_info, $response, $person['pc_eventDate'], $person['pc_endDate'], $person['pc_startTime'], $person['pc_endTime'], $sdate);

        $db_loginsert = sqlStatement($sql_loginsert, $safe);
        $process->resetBackgroundService();
    }
}

