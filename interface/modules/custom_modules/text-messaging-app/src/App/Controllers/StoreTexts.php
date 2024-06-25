<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\App\Controllers;

use OpenEMR\Common\Database\QueryUtils;

class StoreTexts
{
    /**
     * @param array $response
     * @param string|null $db
     * @return void
     */
    public function saveText(array $response, ?string $db)
    {
        if (!isset($db)) {
            require_once dirname(__FILE__, 8) . "/sites/" . $_SESSION["site_id"] . "/sqlconf.php";
            $db = escapeshellarg($sqlconf["dbase"]);
        }
        $statement = self::dbQuery($db);

        $binding = [NULL, $response['fromNumber'], $response['text']];
        QueryUtils::sqlInsert($statement, $binding);
    }

    public function saveOutbound($number, $message): void
    {
        $binding = [$_SESSION['authUser'] ?? 'cron', "+1".$number, $message];
        $statement = self::dbQuery($_SESSION['site_id']);
        //Debugging
        //error_log("Saving " . $message . " " . $_SESSION['site_id']);
        QueryUtils::sqlInsert($statement, $binding);
    }

    private static function dbQuery($db)
    {
        return "INSERT INTO " . $db . ".text_message_module (`id`, `provider_id`, `fromnumber`, `text`, `date`) VALUES (NULL, ?, ?, ?, NOW())";
    }
}
