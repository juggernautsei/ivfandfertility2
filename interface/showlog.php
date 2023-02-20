<?php

$ignoreAuth = true;
require_once("globals.php");

use OpenEMR\Common\Crypto\CryptoGen;

$cryptoGen = new CryptoGen();

$getComment = sqlQuery("SELECT comments FROM log WHERE id = 4");


$trans_comments = $cryptoGen->decryptStandard($getComment["comments"]);

var_dump($trans_comments);