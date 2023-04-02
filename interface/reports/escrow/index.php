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
    use OpenEMR\OeUI\OemrUI;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

</head>
<body>
    <div id="container_div" class="<?php echo $oemr_ui->oeContainer();?> m-5">
        <div class="row">
            <div class="col-sm-12">
                <?php
                    require_once dirname(__FILE__, 3) . "/patient_file/summary/dashboard_header.php";
                ?>
            </div>
        </div>

        <div id="showescrowpayments" class="row" >
            <table class="table table-striped">
                <th><?php echo "Ar Session"?></th>
                <th><?php echo "Date"?></th>
                <th><?php echo "Method"?></th>
                <th><?php echo "Amount"?></th>
            <?php
                $showAllPayments = new Escrow();
                $amounts = $showAllPayments->retrieveAllEscrowPayments();
                foreach ($amounts as $amount) {
                    echo "<tr>";
                    echo "<td>" . $amount['session_id'] . "</td><td>" . $amount['check_date'] . "</td><td>" . $amount['payment_method'] . "</td><td>" . $amount['pay_total'] . "</td>";
                    echo "</tr>";
                }
            ?>
            </table>
        </div>

    </div>
</body>
</html>

