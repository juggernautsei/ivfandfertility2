<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
/*
 *  @package   OpenEMR
 *  @link      http://www.open-emr.org
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 */

    require_once dirname(__FILE__, 3) . "/globals.php";
    require_once "Escrow.php";

    use OpenEMR\Core\Header;
    use OpenEMR\Escrow\Escrow;
    use OpenEMR\Menu\PatientMenuRole;
    use OpenEMR\OeUI\OemrUI;
?>
<!doctype html>
<html lang="en">
<head>
    <?php Header::setupHeader(); ?>
    <title><?php echo xlt('Escrow Report') ?></title>
    <?php
        $arrOeUiSettings = array(
            'heading_title' => xl('Escrow'),
            'include_patient_name' => true,
            'expandable' => true,
            'expandable_files' => "",//all file names need suffix _xpd
            'action' => "",//conceal, reveal, search, reset, link or back
            'action_title' => "",
            'action_href' => "",//only for actions - reset, link or back
            'show_help_icon' => false,
            'help_file_name' => ""
        );
        $oemr_ui = new OemrUI($arrOeUiSettings);
    ?>
    <style>
        @media only print {
            #demoD { visibility: hidden; }
        }
    </style>
</head>
<body>
    <div id="container_div" class="<?php echo $oemr_ui->oeContainer();?>">
        <div class="row">
            <div class="col-sm-12">
                <?php
                    require_once dirname(__FILE__, 3) . "/patient_file/summary/dashboard_header.php";
                    $list_id = "external_data";
                    $menuPatient = new PatientMenuRole();
                    $menuPatient->displayHorizNavBarMenu();
                ?>
            </div>
        </div>
        <button id="demoD" onclick="window.print()">Print this page</button>
        <div id="showescrowpayments" class="row mt-3" >
            <div class="col-sm-12">

                <table class="table table-striped">
                    <th><?php echo "Ar Session"?></th>
                    <th><?php echo "Date"?></th>
                    <th><?php echo "Method"?>/<?php echo "Encounter"?></th>
                    <th><?php echo "Amount"?></th>
                <?php
                    $showAllPayments = new Escrow();
                    $amounts = $showAllPayments->retrieveAllEscrowPayments();
                    foreach ($amounts as $amount) {
                        echo "<tr>";
                        echo "<td>" . $amount['session_id'] . "</td><td>" . $amount['check_date'] . "</td><td>" . $amount['payment_method'] . "</td><td>" . $amount['pay_total'] . "</td>";
                        echo "</tr>";

                        $showAllPayments->arSessionId = $amount['session_id'];
                        $listPayments = $showAllPayments->getEncounterPayments();
                        $sumOfPayments = 0;
                        $lpayment = 0;
                        $lineAmount = 0;
                        $i = 0;
                        foreach ($listPayments as $payment) {

                            if ($lpayment == $payment) {
                              //  echo "<tr><td></td><td></td><td></td><td>" .  . "</td></tr>";
                                $lineAmount = $lpayment['pay_amount'] + $payment['pay_amount'];
                            echo "<tr>";
                            echo "<td></td><td>" . substr($payment['post_time'], 0, -9) . "</td><td>" . $payment['encounter'] . "</td><td>" . $lineAmount . "</td>";
                            echo "</tr>";
                            echo $i++ . "<br>";
                            }

                                $lpayment = $payment;
                           echo $sumOfPayments = $sumOfPayments + $lineAmount . "<br>";
                        }
                        echo "<tr>";
                        echo "<td>" . xlt('Total Payments') . "</td><td></td><td></td><td>" . $sumOfPayments . ".00</td>";
                        echo "</tr>";
                        $remaining = (int)$amount['pay_total'] - $sumOfPayments;
                        echo "<tr>";
                        echo "<td>" . xlt('Remaining Balance') . "</td><td></td><td></td><td>" . $remaining . ".00</td>";
                        echo "</tr>";
                    }

                ?>
                </table>
            </div>
        </div>

    </div>
</body>
</html>

