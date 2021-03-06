<?php

/**
 * Description of SubBinStatistic
 * 
 * v.1.0.0 2015-10-17
 *
 * @author ninthday <bee.me@ninthday.info>
 * @copyright (c) 2015, ninthday
 * @version Release: 1.0.0
 * @since Class available since Release 1.0.0
 */

namespace ninthday\niceTcatBar;

require_once _APP_PATH . 'classes/BinManager.Class.php';

use ninthday\niceTcatBar\BinManager;

class SubBinStatistic
{

    private $pdoConn = null;

    function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->pdoConn = $pdoConn;
    }

    public function getTimeSeries($binID, $condition)
    {
        $objBinMgr = new BinManager($this->pdoConn);
        $binName = $objBinMgr->getBinNameByID($binID);

        $conditionDate = $this->transConditionDate($condition);

        $sql = 'SELECT COUNT(*) AS `nrOfTweets`, COUNT(DISTINCT `from_user_id`) AS `nrOfUsers` '
                . ', COUNT(`retweet_id`) AS `nrOfRetweets` ';
        if ($condition['resolution'] == 'day') {
            $sql .= ', DATE_FORMAT(`created_at`, \'%Y-%m-%d\') `datepart`';
        } elseif ($condition['resolution'] == 'hour') {
            $sql .= ', DATE_FORMAT(`created_at`, \'%Y-%m-%d %H:00\') `datepart`';
        }
        $sql .= ' FROM `' . $binName . '_tweets` `t`';
        $sql .= $this->getSQLWhereCondition($condition);

        $sql .= ' GROUP BY `datepart` ORDER BY `datepart` ';

        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getContains($binID, $condition)
    {
        $objBinMgr = new BinManager($this->pdoConn);
        $binName = $objBinMgr->getBinNameByID($binID);
        $nrOfTweets = $this->getTotalTweets($binName, $condition);
        $nrOfMentions = $this->getContainMentions($binName, $condition);
        $nrOfHashtags = $this->getContainHashtags($binName, $condition);
        $nrOfMedias = $this->getContainMedias($binName, $condition);
        $rtn = array(
            'nrOfTweets' => intval($nrOfTweets),
            'nrOfMentions' => intval($nrOfMentions),
            'nrOfHashtags' => intval($nrOfHashtags),
            'nrOfMedias' => intval($nrOfMedias)
        );
        return $rtn;
    }

    public function getLanguage($binID, $condition)
    {
        $objBinMgr = new BinManager($this->pdoConn);
        $binName = $objBinMgr->getBinNameByID($binID);
        $nrOfTweets = $this->getTotalTweets($binName, $condition);

        $conditionDate = $this->transConditionDate($condition);

        $sql = 'SELECT `lang`, COUNT(*) AS `cnt` FROM `' . $binName . '_tweets` `t`';
        $sql .= $this->getSQLWhereCondition($condition);
        $sql .= ' GROUP BY `lang` ORDER BY `cnt` DESC LIMIT 0,6 ';
        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        $rtn = array();
        $sumLang = 0;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($rtn,
                    array(
                'lang' => $row['lang'],
                'cnt' => intval($row['cnt'])
            ));
            $sumLang += intval($row['cnt']);
        }

        array_push($rtn,
                array(
            'lang' => 'other',
            'cnt' => ($nrOfTweets - $sumLang)
        ));
        return $rtn;
    }

    public function getSubBinBscicInfo($binID, $condition)
    {
        $objBinMgr = new BinManager($this->pdoConn);
        $binName = $objBinMgr->getBinNameByID($binID);
        $nrOfTweets = $this->getTotalTweets($binName, $condition);
        $nrOfUsers = $this->getTotalUniqueUsers($binName, $condition);
        return array(
            'nrOfTweets' => intval($nrOfTweets),
            'nrOfUsers' => intval($nrOfUsers)
        );
    }

    protected function getSQLWhereCondition($condition)
    {
        $whereCondition = '';
        $langs = explode('+', $condition['languages']);

        $whereCondition .= ' WHERE (`t`.`created_at` >= :dateStart AND `t`.`created_at` <= :dateEnd)';

        //Search Keyword Condition
        if (!empty($condition['search_keyword'])) {
            $whereCondition .= ' AND `t`.`text` LIKE :searchKeyword';
        }

        // From user Condition
        if (!empty($condition['from_user'])) {
            $whereCondition .= ' AND `from_user_name` = :fromUser';
        }

        // Languages Condition
        if (count($langs) < 5) {
            if (in_array('other', $langs)) {
                $diffLangs = array_diff(array('en', 'zh', 'zhTW', 'ja', 'other'),
                        $langs);
                $whereCondition .= ' AND `lang` NOT IN (\'' . implode('\', \'',
                                $diffLangs) . '\')';
            } else {
                $whereCondition .= ' AND `lang` IN (\'' . implode('\', \'',
                                $langs) . '\')';
            }
        }
        return $whereCondition;
    }

    protected function transConditionDate($condition)
    {
        return array(
            'date_start' => $condition['date_start'] . ' 00:00:00',
            'date_end' => $condition['date_end'] . ' 23:59:59'
        );
    }

    protected function getTotalTweets($binName, $condition)
    {
        $conditionDate = $this->transConditionDate($condition);

        $sql = 'SELECT COUNT(*) AS `nrOfTweets` FROM `' . $binName . '_tweets` `t`';
        $sql .= $this->getSQLWhereCondition($condition);
        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s tweets.');
        }
        return $results['nrOfTweets'];
    }

    protected function getTotalUniqueUsers($binName, $condition)
    {
        $conditionDate = $this->transConditionDate($condition);
        $sql = 'SELECT COUNT(DISTINCT `from_user_id`) AS `nrOfUsers` FROM `' . $binName . '_tweets` `t`';
        $sql .= $this->getSQLWhereCondition($condition);
        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s Unique users.');
        }
        return $results['nrOfUsers'];
    }

    protected function getContainMentions($binName, $condition)
    {
        $conditionDate = $this->transConditionDate($condition);
        $sql = 'SELECT COUNT(DISTINCT `tweet_id`) AS `nrOfMentions` FROM `' . $binName . '_mentions` `m`'
                . 'INNER JOIN `' . $binName . '_tweets` `t` ON `t`.`id` = `m`.`tweet_id`';
        $sql .= $this->getSQLWhereCondition($condition);

        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s Mentions.');
        }
        return $results['nrOfMentions'];
    }

    protected function getContainHashtags($binName, $condition)
    {
        $conditionDate = $this->transConditionDate($condition);
        $sql = 'SELECT COUNT(DISTINCT `tweet_id`) AS `nrOfHashtags` FROM `' . $binName . '_hashtags` `h`'
                . 'INNER JOIN `' . $binName . '_tweets` `t` ON `t`.`id` = `h`.`tweet_id`';
        $sql .= $this->getSQLWhereCondition($condition);
        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s Hashtags.');
        }
        return $results['nrOfHashtags'];
    }

    protected function getContainMedias($binName, $condition)
    {
        $conditionDate = $this->transConditionDate($condition);
        $sql = 'SELECT COUNT(DISTINCT `tweet_id`) AS `nrOfMedias` FROM `' . $binName . '_media` `m`'
                . 'INNER JOIN `' . $binName . '_tweets` `t` ON `t`.`id` = `m`.`tweet_id`';
        $sql .= $this->getSQLWhereCondition($condition);

        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $conditionDate['date_start'],
                \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $conditionDate['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $keyword = '%' . $condition['search_keyword'] . '%';
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }
        $stmt->execute();
        if (!$results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            throw new Exception('Problem in getting number of ' . $binName . '\'s Medias.');
        }
        return $results['nrOfMedias'];
    }

    public function __destruct()
    {
        $this->pdoConn = null;
        unset($this->pdoConn);
    }

}
