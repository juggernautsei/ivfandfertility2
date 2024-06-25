<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All rights reserved
 */


//use Juggernaut\App\Controllers\Home;
//use Juggernaut\App\Controllers\Invoice;
use Juggernaut\App\Controllers\Notification;
use Juggernaut\App\Controllers\TextIndividuals;
use Juggernaut\App\Controllers\Texting;
use Juggernaut\App\Exceptions\RouteNotFoundException;
use Juggernaut\App\Home;

require_once dirname(__DIR__, 4) . '/globals.php';
require_once __DIR__ . '/../vendor/autoload.php';

const VIEW_PATH = __DIR__ . '/../views';

$router = new Juggernaut\App\Router();

$router
    ->get('/interface/modules/custom_modules/text-messaging-app/public/index.php/home', [Home::class, 'index'])
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/notifications',
        [Notification::class, 'index']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/auditlog',
        [Notification::class, 'auditlog']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/invoices',
        [Invoice::class, 'index']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/invoices/create',
        [Invoice::class, 'create']
    )
    ->post(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/invoices/create',
        [Invoice::class, 'store']
    )
    ->post(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/texting/bulk',
        [Texting::class, 'bulk']
    )
    ->post(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/texting/individualPatient',
        [Texting::class, 'individualPatient']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/individuals',
        [TextIndividuals::class, 'index']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/texting/sendTelehealthMessage',
        [Texting::class, 'sendTelehealthMessage']
    )
    ->get(
        '/interface/modules/custom_modules/text-messaging-app/public/index.php/texting/directTelehealthMessage',
        [Texting::class, 'directTelehealthMessage']
    );


try {
    echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
} catch (RouteNotFoundException $e) {
    echo $e->getMessage();
}
