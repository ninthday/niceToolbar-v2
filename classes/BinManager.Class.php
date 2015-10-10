<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BinManager
 *
 * @author ninthday <bee.me@ninthday.info>
 * @copyright (c) 2015, ninthday
 * @version Release: 1.0.0
 * @since Class available since Release 1.0.0
 */

namespace ninthday\niceTcatBar;

require_once _APP_PATH . 'classes/TcatBin.Class.php';

use ninthday\niceTcatBar\TcatBin;

class BinManager
{

    private $dbh = null;

    function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->dbh = $pdoConn->dbh;
    }

    public function getBinByBinID($binID)
    {
        $sql = 'SELECT `tcat_query_bins`.`id`, `querybin`, `type`, `active`'
                . ', `comments`, `starttime`, `endtime` FROM `tcat_query_bins` '
                . 'INNER JOIN `tcat_query_bins_periods` ON `tcat_query_bins_periods`.`querybin_id` = `tcat_query_bins`.`id` '
                . 'WHERE `tcat_query_bins`.`id` = :binID';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':binID', $binID, \PDO::PARAM_INT);
        $stmt->execute();
        if (!$result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting Bin profile, Bin ID is ' . $binID . '.');
        } else {
            $objBin = new TcatBin();
            $objBin->binID = $binID;
            $objBin->binName = $result['querybin'];
            $objBin->binType = $result['type'];
            $objBin->activeState = $result['active'];
            $objBin->periodStart = $result['starttime'];
            $objBin->periodEnd = $result['endtime'];
            $objBin->binComment = $result['comments'];
            $nbtNDatatime = $this->getNbrOfTweetsDataTime($objBin->binName);
            $objBin->nrOfTweets = $nbtNDatatime['nrOfTweets'];
            $objBin->dataStart = $nbtNDatatime['dataStart'];
            $objBin->dataEnd = $nbtNDatatime['dataEnd'];
            $objBin->binPhrases = $this->getPhrases($binID, $objBin->binType,
                    $objBin->activeState);
        }
        return $objBin;
    }

    protected function getNbrOfTweetsDataTime($binName)
    {
        $sql = "SELECT COUNT(*) AS `nrOfTweets`, MIN(`created_at`) AS `dataStart`, "
                . "MAX(`created_at`) AS `dataEnd` FROM `" . $binName . "_tweets`";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s tweets.');
        }
        return $results;
    }

    public function getSimpleStatistic($binID)
    {
        $rtn = array();
        $binName = $this->getBinNameByID($binID);
        $sql = 'SELECT COUNT(`id`) AS `nrOfTweets`, COUNT(DISTINCT `from_user_id`) AS `nrOfUsers`'
                . 'FROM `' . $binName . '_tweets`';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s tweets.');
        }
        $rtn['nrOfTweets'] = intval($results['nrOfTweets']);
        $rtn['nrOfUsers'] = intval($results['nrOfUsers']);
        unset($stmt);

        $sql = 'SELECT (SELECT COUNT(DISTINCT `tweet_id`) FROM `' . $binName . '_urls`) AS `nrOfURLs`,'
                . '(SELECT COUNT(DISTINCT `tweet_id`) FROM `' . $binName . '_mentions`) AS `nrOfMentions`,'
                . '(SELECT COUNT(DISTINCT `tweet_id`) FROM `' . $binName . '_hashtags`) AS `nrOfHashtags`,'
                . '(SELECT COUNT(DISTINCT `tweet_id`) FROM `' . $binName . '_media`) AS `nrOfMedias`';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s tweets.');
        }
        $rtn['nrOfURLs'] = intval($results['nrOfURLs']);
        $rtn['nrOfMentions'] = intval($results['nrOfMentions']);
        $rtn['nrOfHashtags'] = intval($results['nrOfHashtags']);
        $rtn['nrOfMedias'] = intval($results['nrOfMedias']);

        return $rtn;
    }

    protected function getBinNameByID($binID)
    {
        $sql = 'SELECT `querybin` FROM `tcat_query_bins` WHERE `id`= :binID';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':binID', $binID, \PDO::PARAM_INT);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting BinName by ID: ' . $binID . ' tweets.');
        }
        return $results['querybin'];
    }

    protected function getPhrases($binID, $binType, $activeState)
    {
        $rtn = array();
        switch ($binType) {
            case 'search':
                if ($activeState) {
                    $rtn = $this->getPhrasesFromSearchQueue($binID);
                } else {
                    $rtn = $this->getPhrasesFromSearchArchived($binID);
                }
                break;
            default:
                break;
        }
        return $rtn;
    }

    protected function getPhrasesFromSearchArchived($binID)
    {
        $rtn = array();
        $sql = 'SELECT `origin_phrase` FROM `tcat_search_archives` WHERE `querybin_id` = :binID';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':binID', $binID, \PDO::PARAM_INT);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new \Exception('Problem in getting Phrases from ID: ' . $binID . ' Bin.');
        } else {
            $rtn = explode(' OR ', $results['origin_phrase']);
        }
        return $rtn;
    }

    protected function getPhrasesFromSearchQueue($binID)
    {
        $rtn = array();
        $sql = 'SELECT `origin_phrase` FROM `tcat_search_queues` WHERE `querybin_id` = :binID';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':binID', $binID, \PDO::PARAM_INT);
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new \Exception('Problem in getting Phrases from ID: ' . $binID . ' Bin.');
        } else {
            $rtn = explode(' OR ', $results['origin_phrase']);
        }
        return $rtn;
    }

    /**
     * 解構子歸還資源
     */
    public function __destruct()
    {
        $this->dbh = null;
        unset($this->dbh);
    }

}
