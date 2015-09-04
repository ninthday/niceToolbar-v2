<?php

/**
 * 分析 URL 的類別
 *
 * @author ninthday <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2014, Jeffy Shih
 * @version 1.0
 */

namespace ninthday\niceToolbar;

class URLStatistic
{

    private $dbh = null;

    /**
     * 建構子包含連線設定
     * @param \Floodfire\myPDOConn $pdoConn myPDOConn object
     */
    public function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->dbh = $pdoConn->dbh;
    }

    /**
     * 取得資料庫中資料表名稱為 _urls 結尾的資料表名稱，不包含 _urls
     * 
     * @return array 0:資料表名稱
     * @throws \Exception
     * @access public
     * @since version 1.0
     */
    public function getAllURLTableName()
    {
        $aryRtn = array();
        $sql = "SHOW TABLES LIKE '%_urls' ";
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $rs = $stmt->fetchAll(\PDO::FETCH_NUM);
            foreach ($rs as $row) {
                array_push($aryRtn, str_replace('_urls', '', $row[0]));
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 資料表的基本統計資料
     * 
     * @param array $aryTables 所有要統計的資料表名稱
     * @return array 資料的狀態
     * @access public
     * @since version 1.0
     */
    public function getTablesStatus(array $aryTables)
    {
        $aryRtn = array();
        foreach ($aryTables as $strTableName) {
            $aryDuration = $this->getDuration($strTableName);
            $aryBasic = $this->getBasicNum($strTableName);
            array_push($aryRtn, array(
                'table_name' => $strTableName,
                'duration' => $aryDuration,
                'basic' => $aryBasic
            ));
        }
        return $aryRtn;
    }

    /**
     * 依資料集名稱取得基本統計資料
     * 
     * @param string $strTableName
     * @return array 資料集狀態
     * @access public
     * @since version 1.0
     */
    public function getStatusByDataset($strTableName)
    {
        $aryRtn = array();
        $aryDuration = $this->getDuration($strTableName);
        $aryBasic = $this->getBasicNum($strTableName);
        $aryRtn['duration'] = $aryDuration;
        $aryRtn['basic'] = $aryBasic;
        return $aryRtn;
    }

    /**
     * 由資料集中取得每日URL頻率統計
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @return array
     * @throws \Exception
     */
    public function getDailyURLFreq($strTableName, $strBeginDay, $strEndDay)
    {
        $aryRtn = array();
        $sql = "SELECT DATE_FORMAT(`created_at`, '%Y-%m-%d') AS `BYDAY`, COUNT(*) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " .
                "GROUP BY `BYDAY`";

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                array_push(
                        $aryRtn, array(
                    strtotime($row[0] . " 00:00:00") * 1000,
                    (int) $row[1]
                        )
                );
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 由資料集中取得每日發文者頻率統計（不重複）
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @return array
     * @throws \Exception
     */
    public function getDailyUserFreq($strTableName, $strBeginDay, $strEndDay)
    {
        $aryRtn = array();
        $sql = "SELECT DATE_FORMAT(`created_at`, '%Y-%m-%d') AS `BYDAY`, COUNT(DISTINCT `from_user_id`) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " .
                "GROUP BY `BYDAY`";

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                array_push(
                        $aryRtn, array(
                    strtotime($row[0] . " 00:00:00") * 1000,
                    (int) $row[1]
                        )
                );
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 由資料集中取得每小時頻率統計
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @return array
     * @throws \Exception
     */
    public function getHourlyURLFreq($strTableName, $strBeginDay, $strEndDay)
    {
        $aryRtn = array();
        $sql = "SELECT DATE_FORMAT(`created_at`, '%Y-%m-%d %H:00:00') AS `BYDAY`, COUNT(*) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " .
                "GROUP BY `BYDAY`";

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                array_push(
                        $aryRtn, array(
                    strtotime($row[0]) * 1000,
                    (int) $row[1]
                        )
                );
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 由資料集中取得每小時發文者頻率統計（不重複）
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @return array
     * @throws \Exception
     */
    public function getHourlyUserFreq($strTableName, $strBeginDay, $strEndDay)
    {
        $aryRtn = array();
        $sql = "SELECT DATE_FORMAT(`created_at`, '%Y-%m-%d %H:00:00') AS `BYDAY`, COUNT(DISTINCT `from_user_id`) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " .
                "GROUP BY `BYDAY`";

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                array_push(
                        $aryRtn, array(
                    strtotime($row[0]) * 1000,
                    (int) $row[1]
                        )
                );
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 取得指定時間內前N名的 Domain 和數量統計
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @param int $intTop 前 N 筆資料
     * @return array
     * @throws \Exception
     */
    public function getDailyTopNDomain($strTableName, $strBeginDay, $strEndDay, $intTop)
    {
        $aryRtn = array();
        $sql = "SELECT `domain`, COUNT(*) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `error_code` LIKE '2%' AND `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " . 
                "GROUP BY `domain` ORDER BY `CNT` DESC Limit 0, " . $intTop;

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($aryRtn, $row);
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        
        return $aryRtn;
    }
    
    /**
     * 取得指定時間內前N名的 URL 和數量統計
     * 
     * @param string $strTableName 資料集名稱
     * @param string $strBeginDay 資料區間開始日期（yyyy-mm-dd）
     * @param string $strEndDay 資料區間結束日期（yyyy-mm-dd）
     * @param int $intTop 前 N 筆資料
     * @return array
     * @throws \Exception
     */
    public function getTopURLs($strTableName, $strBeginDay, $strEndDay, $intTop)
    {
        $aryRtn = array();
        $sql = "SELECT `url_followed`, COUNT(*) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `error_code` LIKE '2%' AND `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " . 
                "GROUP BY `url_followed` ORDER BY `CNT` DESC Limit 0, " . $intTop;

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($aryRtn, $row);
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        
        return $aryRtn;
    }
    
    /**
     * 取得指定時間內前N名的發文者和數量統計
     * 
     * @param string $strTableName
     * @param string $strBeginDay
     * @param string $strEndDay
     * @param int $intTop
     * @return array
     * @throws \Exception
     */
    public function getTopPoster($strTableName, $strBeginDay, $strEndDay, $intTop)
    {
        $aryRtn = array();
        $sql = "SELECT `from_user_name`, COUNT(*) AS `CNT` FROM `" . $strTableName . "_urls` " .
                "WHERE `error_code` LIKE '2%' AND `created_at` > '" . $strBeginDay . " 00:00:00' AND `created_at` <= '" . $strEndDay . " 23:59:59' " . 
                "GROUP BY `from_user_name` ORDER BY `CNT` DESC Limit 0, " . $intTop;

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($aryRtn, $row);
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        
        return $aryRtn;
    }

    /**
     * 取得指定資料表最早和最晚一筆的時間
     * 
     * @param type $strTableName 資料表名稱
     * @return type
     * @throws \Exception
     * @access private
     * @since version 1.0
     */
    private function getDuration($strTableName)
    {
        $sql = "SELECT MIN(`created_at`) AS `begin`, MAX(`created_at`) AS `end` FROM `" . $strTableName . "_urls`";

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_ASSOC);
            $aryRtn = array(
                'begin' => $rs['begin'],
                'end' => $rs['end']
            );
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 指定資料表的基本資料
     * 
     * @param string $strTableName 資料表名稱
     * @return array total: 總筆數, unshorten: 已短網址還原避暑
     * @throws \Exception
     */
    private function getBasicNum($strTableName)
    {
        $aryRtn = array();
        try {
            // 取得資料表資料總數
            $sql_total = "SELECT COUNT(*) FROM `" . $strTableName . "_urls`";
            $stmt = $this->dbh->prepare($sql_total);
            $stmt->execute();
            $rs_total = $stmt->fetch(\PDO::FETCH_NUM);
            $aryRtn['total'] = $rs_total[0];

            // 取得資料表成功反解短網址的數量
            $sql_stat = "SELECT COUNT(CASE WHEN `error_code` = 200 THEN `id` ELSE NULL END) AS `Done`, " .
                    "COUNT(CASE WHEN `error_code` IS NULL THEN `id` ELSE NULL END) AS `INProc`, " .
                    "COUNT(CASE WHEN `error_code` <> 200 AND `error_code` IS NOT NULL THEN `id` ELSE NULL END) AS `Error` " .
                    "FROM `" . $strTableName . "_urls`";
            $stmt = $this->dbh->prepare($sql_stat);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_NUM);
            $aryRtn['unshorten'] = $rs[0];
            $aryRtn['inproc'] = $rs[1];
            $aryRtn['error'] = $rs[2];
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $aryRtn;
    }

    /**
     * 解構子歸還資源
     */
    public function __destruct()
    {
        $this->dbh = null;
    }

}
