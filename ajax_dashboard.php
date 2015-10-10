<?php

session_start();
require './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
    $userData = $objOAuthService->userinfo->get();
} else {
    header('Location: ' . _WEB_ADDR . 'gauth.php');
}

$opt = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);

require_once _APP_PATH . 'classes/myPDOConn.Class.php';
require_once _APP_PATH . 'classes/Dashboard.Class.php';
require_once _APP_PATH . 'classes/BinManager.Class.php';

use ninthday\niceToolbar\myPDOConn;
use ninthday\niceTcatBar\Dashboard;
use ninthday\niceTcatBar\BinManager;

try {
    $tcatPDOConn = myPDOConn::getInstance('tcatPDOConnConfig.inc.php');
    switch ($opt) {
        case 'top':
            $topN = intval(filter_input(INPUT_GET, 'nr', FILTER_SANITIZE_NUMBER_INT));
            $objBoard = new Dashboard($tcatPDOConn);
            $tcatBins = $objBoard->getBordBinList($topN);
            $aryResult['rsStat'] = true;
            $aryResult['rsContents'] = $tcatBins;
            break;
        case 'chart':
            $binID = intval(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
            $objBinMgr = new BinManager($tcatPDOConn);
            $binStatistic = $objBinMgr->getSimpleStatistic($binID);
            $aryResult['rsStat'] = true;
            $aryResult['rsContents'] = $binStatistic;
            break;
        default :
            break;
    }
} catch (Exception $exc) {
    $aryResult['rsStat'] = false;
    $aryResult['rsContents'] = $exc->getMessage();
}

unset($tcatPDOConn);

header("Content-type: application/json; charset=utf-8");

echo json_encode($aryResult);
