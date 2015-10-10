<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dashboard
 *
 * @author ninthday <bee.me@ninthday.info>
 * @copyright (c) 2015, ninthday
 * @version Release: 1.0.0
 * @since Class available since Release 1.0.0
 */

namespace ninthday\niceTcatBar;

require_once _APP_PATH . 'classes/BinManager.Class.php';

use ninthday\niceTcatBar\BinManager;

class Dashboard
{
    private $pdoConn;

    function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->pdoConn = $pdoConn;
    }

    public function getBordBinList($topN)
    {
        $rtn = array();
        $binIDs = $this->getTopNBinID($topN);
        $objBinMgr = new BinManager($this->pdoConn);
        foreach ($binIDs as $binID) {
            $objBin = $objBinMgr->getBinByBinID($binID);
            array_push($rtn, $objBin);
        }
        return $rtn;
    }

    protected function getTopNBinID($topN)
    {
        $rtn = array();
        $sql = 'SELECT `id` FROM `tcat_query_bins` ORDER BY `id` DESC LIMIT 0,:topN';
        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':topN', $topN, \PDO::PARAM_INT);
        $stmt->execute();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($rtn, $row['id']);
        }
        return $rtn;
    }

    function __destruct()
    {
        $this->pdoConn = null;
        unset($this->pdoConn);
    }

}
