<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\App\Controllers;


class TextAppointmentStatusChange
{
    private array $change;
    public function __construct($data)
    {
        $this->change = $data;
        $this->contactPatient(new SendMessage());
    }
    private function contactPatient(SendMessage $sendMessage): void
    {
        $phone = self::getPatientCell();
        $message = self::updatePatientScheduleMsg();
        $sendMessage::outBoundMessage($phone, $message);
    }
    private function getPatientCell(): string
    {
        $sql = "SELECT `phone_cell` FROM `patient_data` WHERE `pid` = ?";
        return sqlQuery($sql, $this->change['form_pid']);
    }

    private function updatePatientScheduleMsg(): string
    {
        return "Your " . $this->change['form_title'] . " appointment has been rescheduled to "
            . $this->change['event_start_data']
            . " Please add to your calendar. Or text back with reply";
    }
}
