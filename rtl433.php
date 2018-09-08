<?php

/**
 * Main project script
 *
 * @package MajorDoMo
 * @author Serge Dzheigalo <jey@tut.by> http://smartliving.ru/
 * @version 1.1
 */

include_once("./config.php");
include_once("./lib/loader.php");

// start calculation of execution time
startMeasure('TOTAL');

include_once(DIR_MODULES . "application.class.php");

$session = new session("prj");



// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);

include_once("./load_settings.php");

if ($_REQUEST['json'])
{ $json=$_REQUEST['json'];
SQLexec("update rtl433_config set VALUE='$json' where parametr='JSON'");
}


// closing database connection
$db->Disconnect();

