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
require_once _APP_PATH . 'classes/SubBinStatistic.Class.php';

use ninthday\niceToolbar\myPDOConn;
use ninthday\niceTcatBar\SubBinStatistic;

$aryResult = array();

try {
    $tcatPDOConn = myPDOConn::getInstance('tcatPDOConnConfig.inc.php');
    $binID = intval(filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_NUMBER_INT));
    $condition = array(
        'date_start' => filter_input(INPUT_GET, 'ds', FILTER_SANITIZE_STRING),
        'date_end' => filter_input(INPUT_GET, 'de', FILTER_SANITIZE_STRING),
        'search_keyword' => filter_input(INPUT_GET, 'sk', FILTER_SANITIZE_STRING),
        'from_user' => filter_input(INPUT_GET, 'fu', FILTER_SANITIZE_STRING),
        'languages' => filter_input(INPUT_GET, 'lg', FILTER_SANITIZE_STRING),
        'resolution' => filter_input(INPUT_GET, 'res', FILTER_SANITIZE_STRING)
    );
    switch ($opt) {
        case 'ts':
            $objSBStis = new SubBinStatistic($tcatPDOConn);
            $result = $objSBStis->getTimeSeries($binID, $condition);

            $xCategory = array();
            $series = array(
                'nrOfTweets' => array(),
                'nrOfUsers' => array(),
                'nrOfRetweets' => array()
            );
            foreach ($result as $row) {
                $xCategory[] = $row['datepart'];
                $series['nrOfTweets'][] = intval($row['nrOfTweets']);
                $series['nrOfUsers'][] = intval($row['nrOfUsers']);
                $series['nrOfRetweets'][] = intval($row['nrOfRetweets']);
            }
            $aryResult['rsStat'] = true;
            $aryResult['rsContents']['xCategory'] = $xCategory;
            $aryResult['rsContents']['series'] = $series;
            break;
        case 'ctn':
            $objSBStis = new SubBinStatistic($tcatPDOConn);
            $result = $objSBStis->getContains($binID, $condition);
            
            $aryResult['rsStat'] = true;
            $aryResult['rsContents'] = $result;
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
