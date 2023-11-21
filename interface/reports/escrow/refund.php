<?php

/**
 *  @package   OpenEMR
 *  @link      http://www.open-emr.org
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 */
require_once dirname(__FILE__, 3) . "/globals.php";
require_once "Escrow.php";

use OpenEMR\Core\Header;
use OpenEMR\Escrow\Escrow;

$setEscrowRefundValues = new Escrow();

if ((isset($_POST['checkno']) && isset($_POST['amount']) && (!empty($_POST['checkno']) && !empty($_POST['amount'])))) {
    $setEscrowRefundValues->reference = $_POST['checkno'];
    $setEscrowRefundValues->payTotal = $_POST['amount'];
    $setEscrowRefundValues->checkDate = $_POST['checkdate'];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Escrow Refund'); ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3><?php echo xlt("Refund") ?></h3>
            <form class="form-group" method="post" action="refund.php">
                <label for="Check#"><?php echo xlt("Check Date") ?>#
                    <input type="text" class="form-control w-50" name="checkdate" >
                </label>
                <label for="Check#"><?php echo xlt("Check") ?>#
                    <input type="text" class="form-control w-50" name="checkno" >
                </label>
                <label for="amount"><?php echo xlt("Amount") ?>
                    <input type="text" class="form-control" name="amount" >
                </label>
                <input type="submit" class="btn btn-primary" value="<?php echo xlt("Refund") ?>">
            </form>
        </div>
    </div>
</div>
</body>
</html>

