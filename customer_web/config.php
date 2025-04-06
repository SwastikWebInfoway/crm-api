<?php 
    @session_start();

    if(!isset($_POST['is_debug']) || $_POST['is_debug'] != 1) {
        error_reporting(0);
    }

    define("VERSION","v3");
    set_time_limit(727379969012);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    include_once("api-constants.php");
    
    $pdo = '';
    // Ajax response array (No need to set response array in every individual file)
    $ajaxResponse = array();
    $ajaxResponse["status"]     = 0;
    $ajaxResponse["message"]    = "";
    $ajaxResponse["data"]       = array();
    
    include_once(ROOT_CLASS . "class.database.php");    
    include_once(ROOT_CLASS . "class.helper.php");
  

    $helper = new Helper("PHP_ARRAY");
