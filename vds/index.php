<?php

session_start();

require_once('settings.php');
require_once('classes/VDS.class.php');

ini_set('display_errors',  DEV_MODE);
ini_set('display_startup_errors', DEV_MODE);
error_reporting(DEV_MODE?'E_ALL':'E_USER');

try{
    $vds = new \VDS\VDS((isset($_GET['fp'])?$_GET['fp']:''), PASSWD);
}catch(\Exception $ex){
    echo $ex->getMessage();
}

?>
