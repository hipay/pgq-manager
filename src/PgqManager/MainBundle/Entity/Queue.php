<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\MainBundle\Entity;


class Queue
{
    /**
     * @var string
     */
    private $queueName;
    /**
     * @var int
     */
    private $queueNtables;
    /**
     * @var int
     */
    private $queueCurTable;
    /**
     * @var string
     */
    private $queueRotationPeriod;
    /**
     * @var \Datetime
     */
    private $queueSwitchTime;
    /**
     * @var bool
     */
    private $queueExternalTicker;
    /**
     * @var int
     */
    private $queueTickerMaxCount;
    /**
     * @var string
     */
    private $queueTickerMaxLag;
    /**
     * @var string
     */
    private $queueTickerIdlePeriod;
    /**
     * @var string
     */
    private $tickerLag;

    /**
     * @param int $queueCurTable
     */
    public function setQueueCurTable($queueCurTable)
    {
        $this->queueCurTable = $queueCurTable;
    }

    /**
     * @return int
     */
    public function getQueueCurTable()
    {
        return $this->queueCurTable;
    }

    /**
     * @param boolean $queueExternalTicker
     */
    public function setQueueExternalTicker($queueExternalTicker)
    {
        $this->queueExternalTicker = $queueExternalTicker;
    }

    /**
     * @return boolean
     */
    public function getQueueExternalTicker()
    {
        return $this->queueExternalTicker;
    }

    /**
     * @param string $queueName
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
    }

    /**
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @param int $queueNtables
     */
    public function setQueueNtables($queueNtables)
    {
        $this->queueNtables = $queueNtables;
    }

    /**
     * @return int
     */
    public function getQueueNtables()
    {
        return $this->queueNtables;
    }

    /**
     * @param string $queueRotationPerdiod
     */
    public function setQueueRotationPeriod($queueRotationPerdiod)
    {
        $this->queueRotationPeriod = $queueRotationPerdiod;
    }

    /**
     * @return string
     */
    public function getQueueRotationPerdiod()
    {
        return $this->queueRotationPeriod;
    }

    /**
     * @param \Datetime $queueSwitchTime
     */
    public function setQueueSwitchTime($queueSwitchTime)
    {
        $this->queueSwitchTime = $queueSwitchTime;
    }

    /**
     * @return \Datetime
     */
    public function getQueueSwitchTime()
    {
        return $this->queueSwitchTime;
    }

    /**
     * @param string $queueTickerIdlePeriod
     */
    public function setQueueTickerIdlePeriod($queueTickerIdlePeriod)
    {
        $this->queueTickerIdlePeriod = $queueTickerIdlePeriod;
    }

    /**
     * @return string
     */
    public function getQueueTickerIdlePeriod()
    {
        return $this->queueTickerIdlePeriod;
    }

    /**
     * @param int $queueTickerMaxCount
     */
    public function setQueueTickerMaxCount($queueTickerMaxCount)
    {
        $this->queueTickerMaxCount = $queueTickerMaxCount;
    }

    /**
     * @return int
     */
    public function getQueueTickerMaxCount()
    {
        return $this->queueTickerMaxCount;
    }

    /**
     * @param string $queueTickerMaxLag
     */
    public function setQueueTickerMaxLag($queueTickerMaxLag)
    {
        $this->queueTickerMaxLag = $queueTickerMaxLag;
    }

    /**
     * @return string
     */
    public function getQueueTickerMaxLag()
    {
        return $this->queueTickerMaxLag;
    }

    /**
     * @param string $tickerLag
     */
    public function setTickerLag($tickerLag)
    {
        $this->tickerLag = $tickerLag;
    }

    /**
     * @return string
     */
    public function getTickerLag()
    {
        return $this->tickerLag;
    }

    /**
     * @param array $array
     * @return Queue $this
     */
    public function populate(array $array)
    {
        $this->queueName = $array['queue_name'];
        $this->queueNtables = (int)$array['queue_ntables'];
        $this->queueCurTable = (int)$array['queue_cur_table'];
        $this->queueRotationPeriod = $array['queue_rotation_period'];
        $this->queueSwitchTime = new \DateTime($array['queue_switch_time']);
        $this->queueExternalTicker = (bool)$array['queue_external_ticker'];
        $this->queueTickerMaxCount = (int)$array['queue_ticker_max_count'];
        $this->queueTickerMaxLag = $array['queue_ticker_max_lag'];
        $this->queueTickerIdlePeriod = $array['queue_ticker_idle_period'];
        $this->tickerLag = $array['ticker_lag'];

        return $this;
    }
}