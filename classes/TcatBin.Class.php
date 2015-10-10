<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TcatBin
 *
 * @author ninthday <bee.me@ninthday.info>
 * @copyright (c) 2015, ninthday
 * @version Release: 1.0.0
 * @since Class available since Release 1.0.0
 */

namespace ninthday\niceTcatBar;

class TcatBin implements \JsonSerializable
{

    protected $binID;
    protected $binName;
    protected $binType;
    protected $activeState;
    protected $periodStart;
    protected $periodEnd;
    protected $dataStart;
    protected $dataEnd;
    protected $nrOfTweets;
    protected $binPhrases = array();
    protected $binComment;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            switch ($property) {
                case 'binID':
                    $this->binID = intval($value);
                    break;
                case 'binName':
                    $this->binName = $value;
                    break;
                case 'binType':
                    $this->binType = $value;
                    break;
                case 'activeState':
                    $this->activeState = boolval($value);
                    break;
                case 'periodStart':
                    $this->periodStart = $value;
                    break;
                case 'periodEnd':
                    $this->periodEnd = $value;
                    break;
                case 'dataStart':
                    $this->dataStart = $value;
                    break;
                case 'dataEnd':
                    $this->dataEnd = $value;
                    break;
                case 'nrOfTweets':
                    $this->nrOfTweets = intval($value);
                    break;
                case 'binPhrases':
                    if (is_array($value)) {
                        $this->binPhrases = $value;
                    } else {
                        $this->binPhrases = explode(' OR ', $value);
                    }
                    break;
                case 'binComment':
                    $this->binComment = $value;
                    break;
            }
        } else {
            throw new \InvalidArgumentException('Your attribute is NOT in TcatBin. Attribute was ' . $property);
        }
    }

    public function jsonSerialize()
    {
        return array(
            'bin_id' => $this->binID,
            'bin_name' => $this->binName,
            'bin_type' => $this->binType,
            'active_state' => $this->activeState,
            'period_start' => $this->periodStart,
            'peroid_end' => $this->periodEnd,
            'data_start' => $this->dataStart,
            'data_end' => $this->dataEnd,
            'nbr_tweets' => $this->nrOfTweets,
            'bin_pharses' => $this->binPhrases,
            'bin_comment' => $this->binComment
        );
    }

}
