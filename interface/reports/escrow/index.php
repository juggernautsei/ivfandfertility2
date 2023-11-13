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
            'expandable' => false,
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
        <button class="btn btn-primary mt-3" id="demoD" onclick="window.print()"><?php echo xlt('Print this page'); ?></button>
        <button class="btn btn-primary mt-3" id="refund" onclick=""><?php echo xlt('Refund'); ?></button>
        <div id="showescrowpayments" class="row mt-3" >
            <div class="col-sm-12">

                <table class="table table-striped">
                    <th><?php echo "Transaction ID"?></th>
                    <th><?php echo "Date Deposit/Service"?></th>
                    <th><?php echo "Method"?>/<?php echo "Encounter"?></th>
                    <th><?php echo "Amount"?></th>
                <?php
                    $showAllPayments = new Escrow();
                    $amounts = $showAllPayments->retrieveAllEscrowPayments();
                    foreach ($amounts as $amount) {
                        echo "<tr>";
                        echo "<td>" . $amount['session_id'] . "</td><td>" . $amount['check_date'] . "</td><td>" .
                            $amount['payment_method'] . "</td><td>" . $amount['pay_total'] . "</td>";
                        echo "</tr>";

                        $showAllPayments->arSessionId = $amount['session_id'];
                        $encounters = $showAllPayments->getEncounterEntries();
                        //$listPayments = $showAllPayments->getEncounterPayments();
                        $sumOfPayments = 0;
                        $encounter = 0;
                        $lineAmount = 0;
                        $i = 0;
                        foreach ($encounters as $encounter) {
                            $showAllPayments->encounter = $encounter['encounter'];
                            $paymentsTotals = $showAllPayments->getEncounterPayments();
                            echo "<tr>";
                            echo "<td></td><td>" . substr($paymentsTotals[0]['date'], 0, -9) . "</td><td>" .
                                $paymentsTotals[0]['encounter'] . "</td><td>-" . $paymentsTotals[0]['pay_amount'] . "</td>";
                            echo "</tr>";
                            $sumOfPayments = $sumOfPayments + $paymentsTotals[0]['pay_amount'];
                        }
                        echo "<tr>";
                        echo "<td>" . xlt('Total Payments') . "</td><td></td><td></td><td>-" . $sumOfPayments . ".00</td>";
                        echo "</tr>";
                        //refund segment
                        $refund = $showAllPayments->getRefund();
                        if ($refund) {
                            echo "<tr>";
                            echo "<td>" . xlt('Refund') . "</td><td>" . $refund['check_date'] . "</td><td> Check: " .
                                $refund['reference'] . "</td><td>" . $refund['pay_total'] . "</td>";
                            echo "</tr>";

                        }
                        $lessRefund = (int)$refund['pay_total'];
                        $remaining = (int)$amount['pay_total'] - $sumOfPayments;
                        echo "<tr>";
                        echo "<td>" . xlt('Remaining Balance') . "</td><td></td><td></td><td>" . ($lessRefund + $remaining) . ".00</td>";
                        echo "</tr>";
                    }

                ?>
                </table>
            </div>
        </div>

    </div>
<script>
    $refund = document.getElementById('refund');
    $refund.addEventListener('click', function () {
        let url = '<?php echo $GLOBALS['webroot'] . '/interface/reports/escrow/refund.php'?>';
        dlgopen(url, '_blank', 'modal-sm', 355, '', '', {
            buttons: [
                {text: <?php echo xlj('Close'); ?>, close: true, style: 'default btn-sm'}
            ],
        }).then(() => {
            location.reload();
        })
    });
</script>
</body>
</html>

