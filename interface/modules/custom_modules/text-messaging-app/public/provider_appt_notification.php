<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once dirname(__DIR__, 4) . '/globals.php';
require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once ($GLOBALS['srcdir'] . "/appointments.inc.php");

use Juggernaut\App\Controllers\SendMessage;

function start_appt_reminders()
{
    $providers = sqlStatement("SELECT DISTINCT pc_aid FROM `openemr_postcalendar_events` WHERE pc_aid > 2");

    while ($prow = sqlFetchArray($providers)) {
        $apptDate = date('Y-m-d');
        $events = fetchAppointments($apptDate, $apptDate, '', $prow['pc_aid']);

        $eventsList = '';
        foreach ($events as $event) {
            $eventsList .= $event['pc_catname'] . " " . $event['pc_startTime'] . ", ";
        }
        $message = '';
        if (empty($eventsList)) {
            echo $message = 'None';
        } else {
            echo $message = "Your schedule for today: " . $eventsList;
        }

        $number = sqlQuery("SELECT phonecell FROM `users` WHERE id = ?", [$prow['pc_aid']]);

        if (!empty($number['phonecell'])) {
            $cell = str_replace("-", "", $number['phonecell']);
            SendMessage::outBoundMessage($cell, $message);
        }
    }

sqlStatement("update background_services set running = 0 where name = 'Provider_Reminders'");
}
