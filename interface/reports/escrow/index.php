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

    use OpenEMR\Core\Header;
    use OpenEMR\Escrow\Escrow;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Escrow Report') ?></title>
    <?php echo Header::setupHeader(); ?>
</head>
<body>
    <div class="container m-5">
        <h2><?php echo xlt('Escrow Report'); ?></h2>
        <div id="showescrowpayments"  >
            <?php
                $showAllPayments = new Escrow();
                $amounts = $showAllPayments->retrieveAllEscrowPayments();
                var_dump($amounts);
            ?>
        </div>

    </div>
</body>
</html>

