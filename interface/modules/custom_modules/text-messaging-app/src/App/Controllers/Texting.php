<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All rights reserved
 */

namespace Juggernaut\App\Controllers;


use Juggernaut\App\Model\NotificationModel;

class Texting extends SendMessage
{
    protected StoreTexts $storeTexts;

    public function __construct()
    {
        $this->storeTexts = new StoreTexts();
    }

    public static function bulk(): void
    {
        echo "<title>" . xlt('Texting Results') . "</title>";
        $numbers = $_POST['pnumbers'];
        if (!str_contains($numbers, ",")) {
            die(xlt('Please use a comma separated list'));
        }
        $messagesbody = $_POST['message'];
        $individuals = explode(",", $numbers);
        foreach ($individuals as $individual) {
            if(empty($individual)) {
                continue; //The plan on using it for single messages to patients
            }
            $individual = str_replace("-", "", $individual);
            $response = parent::outBoundMessage($individual, $messagesbody);
            $results = json_decode($response, true);

            echo self::messageResultsDisplay($results);
        }
    }

    //one way message
    public function sendTelehealthMessage()
    {
        $meetingLink = $this->comLinkMeetingLink();

        $data = new NotificationModel();
        $outboundMessage = '';
        $patientNumber = $data->getPatientCell();
        $balance = self::balanceDue();
        if ($balance > 0) {
            $outboundMessage = self::balanceMessage() . " $" . $balance . " Click link to pay ";
            $balance = str_replace('.', '', $balance);
            $outboundMessage .= $_SERVER['SERVER_NAME'] . '/portal/stripe.php?amount=' . $balance;
        }
        if (!empty($patientNumber)) {
            $patientNumber = str_replace('-', '', $patientNumber['phone_cell']);
            $outboundMessage .= self::telehealthMessageBody() .
                self::getTextFacilityInfo()['name'] . ' ' .
                $meetingLink;
            $response = parent::outBoundMessage((int)$patientNumber, $outboundMessage);
            $results = json_decode($response, true);
            if ($results['success'] === true) {
                $this->storeTexts->saveOutbound($patientNumber, $outboundMessage);
            }
            echo self::messageResultsDisplay($results) . ' <br>' . $patientNumber ;
        }
    }

    public function directTelehealthMessage()
    {
        $meetingLink = $this->MeetingLink();

        $data = new NotificationModel();
        $patientNumber = $data->getPatientCell();
        if (!empty($patientNumber)) {
            $patientNumber = str_replace('-', '', $patientNumber['phone_cell']);
            $outboundMessage = self::telehealthMessageBody() .
                self::getTextFacilityInfo()['name'] . ' ' .
                $meetingLink;
            $balance = self::balanceDue();
            if ($balance > 0) {
                $outboundMessage .= self::balanceMessage() . "Balance Due NOW $" . $balance . " before meeting";
            }
            $response = parent::outBoundMessage((int)$patientNumber, $outboundMessage);
            $results = json_decode($response, true);

            echo self::messageResultsDisplay($results) . ' <br>' . $patientNumber ;
        }
    }
    public function individualPatient(): string
    {
        $phone = str_replace('-', '', $_POST['phone']);
        return parent::outBoundMessage($phone, $_POST['messageoutbound']);
    }

    private function telehealthMessageBody(): string
    {
        return xlt(
            "Telehealth Meeting Now: By clicking the link below, you are consenting to the telehealth service. "
            ) .
            xlt(" Please call the office at ") .
            self::getTextFacilityInfo()['phone'] .
            xlt(" if you need portal login access. ");
    }

    private function comLinkMeetingLink(): string
    {
        return "\r\nhttps://" . $_SERVER['HTTP_HOST'] . "/portal/?site=" . $_SESSION['site_id'];
    }

    private function MeetingLink(): string
    {
            $data = new NotificationModel();
            return 'https://' . $_SERVER['HTTP_HOST'] .
                '/interface/modules/custom_modules/oe-telehealth-8x8-jitsi/public/patient.php?q=' .
                $data->createMeetingId() . '&c=' . $_SESSION['pid'];
    }

    private function getTextFacilityInfo()
    {
        return sqlQuery("select `name`, `phone` from `facility` where `id` = 3");
    }

    private static function messageResultsDisplay($results): string
    {
        if ($results['success'] === true) {

            return xlt(" Successful, message ID ") . $results['textId'] .
                " <br>" . xlt("Remaining message ") . $results['quotaRemaining'] .
                "<br>" . xlt('Alert support when this gets to 20');
        } else {
            return xlt(" Message failed ") . $results['error'];
        }
    }

    private function balanceDue()
    {
        require_once dirname(__FILE__, 8) . "/library/patient.inc";
        return get_patient_balance($_SESSION['pid']);
    }

    private function balanceMessage(): string
    {
        return xlt(" Call the office to pay your balance immediately.");
    }
}
