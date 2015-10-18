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
        $langs = explode('+', $condition['languages']);
        $sql = 'SELECT COUNT(*) AS `nrOfTweets`, COUNT(DISTINCT `from_user_id`) AS `nrOfUsers` '
                . ', COUNT(`retweet_id`) AS `nrOfRetweets` ';
        if ($condition['resolution'] == 'day') {
            $sql .= ', DATE_FORMAT(`created_at`, \'%Y-%m-%d\') `datepart` ';
        } elseif ($condition['resolution'] == 'hour') {
            $sql .= ', DATE_FORMAT(`created_at`, \'%Y-%m-%d %H:00\') `datepart` ';
        }
        $sql .= 'FROM `' . $binName . '_tweets` ';
        $sql .= 'WHERE (`created_at` >= :dateStart AND `created_at` <= :dateEnd)';
        
        //Search Keyword Condition
        if (!empty($condition['search_keyword'])) {
            $sql .= ' AND `text` LIKE :searchKeyword';
            $keyword = '%' . $condition['search_keyword'] . '%';
        }
        
        // From user Condition
        if (!empty($condition['from_user'])) {
            $sql .= ' AND `from_user_name` = :fromUser';
        }
        
        // Languages Condition
        if (count($langs) < 4) {
            if (in_array('other', $langs)) {
                $diffLangs = array_diff($langs,
                        array('en', 'zh', 'zh-tw', 'other'));
                $sql .= ' AND `lang` NOT IN (\'' . implode(', ', $diffLangs) . '\')';
            } else {
                $sql .= ' AND `lang` IN (\'' . implode(', ', $langs) . '\')';
            }
        }

        $sql .= " GROUP BY `datepart` ORDER BY `datepart` ";

        $stmt = $this->pdoConn->dbh->prepare($sql);
        $stmt->bindParam(':dateStart', $condition['date_start'], \PDO::PARAM_STR);
        $stmt->bindParam(':dateEnd', $condition['date_end'], \PDO::PARAM_STR);

        if (!empty($condition['search_keyword'])) {
            $stmt->bindParam(':searchKeyword', $keyword, \PDO::PARAM_STR);
        }

        if (!empty($condition['from_user'])) {
            $stmt->bindParam(':fromUser', $condition['from_user'],
                    \PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function __destruct()
    {
        $this->pdoConn = null;
        unset($this->pdoConn);
    }

}
