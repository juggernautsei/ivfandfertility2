<?php

/**
 *  @package   OpenEMR
 *  @link      http://www.open-emr.org
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 */
require_once dirname(__FILE__, 3) . "/globals.php";
use OpenEMR\Core\Header;
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h3><?php echo xlt("Refund") ?></h3>
            <form class="form-group" method="post" action="refund.php">
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

