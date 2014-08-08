<?php
/**
 * Copyright (c) 2014 HiMedia Group
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014 HiMedia Group
 * @author Karl MARQUES <kmarques@hi-media.com>
 * @license Apache License, Version 2.0
 */

namespace PgqManager\MainBundle\Entity;

/**
 * Class Queue
 * @package PgqManager\MainBundle\Entity
 */
class Queue
{
    /**
     * @var int
     */
    private $queueId;
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
    private $queueDataPfx;
    /**
     * @var string
     */
    private $queueEventSeq;
    /**
     * @var string
     */
    private $queueTickSeq;

    private $queueFailedCount;

    private $queueRetryCount;

    private $queueCount;

    private $consumers;

    /**
     * @param mixed $queueId
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
    }

    /**
     * @return mixed
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

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
     * @param string $queueDataPfx
     */
    public function setQueueDataPfx($queueDataPfx)
    {
        $this->queueDataPfx = $queueDataPfx;
    }

    /**
     * @return string
     */
    public function getQueueDataPfx()
    {
        return $this->queueDataPfx;
    }

    /**
     * @param string $queueEventSeq
     */
    public function setQueueEventSeq($queueEventSeq)
    {
        $this->queueEventSeq = $queueEventSeq;
    }

    /**
     * @return string
     */
    public function getQueueEventSeq()
    {
        return $this->queueEventSeq;
    }

    /**
     * @param string $queueTickSeq
     */
    public function setQueueTickSeq($queueTickSeq)
    {
        $this->queueTickSeq = $queueTickSeq;
    }

    /**
     * @return string
     */
    public function getQueueTickSeq()
    {
        return $this->queueTickSeq;
    }

    /**
     * @param mixed $queueFailedCount
     */
    public function setQueueFailedCount($queueFailedCount)
    {
        $this->queueFailedCount = $queueFailedCount;
    }

    /**
     * @return mixed
     */
    public function getQueueFailedCount()
    {
        return $this->queueFailedCount;
    }

    /**
     * @param mixed $queueRetryCount
     */
    public function setQueueRetryCount($queueRetryCount)
    {
        $this->queueRetryCount = $queueRetryCount;
    }

    /**
     * @return mixed
     */
    public function getQueueRetryCount()
    {
        return $this->queueRetryCount;
    }

    /**
     * @param mixed $queueCount
     */
    public function setQueueCount($queueCount)
    {
        $this->queueCount = $queueCount;
    }

    /**
     * @return mixed
     */
    public function getQueueCount()
    {
        return $this->queueCount;
    }

    /**
     * @param mixed $consumers
     */
    public function setConsumers($consumers)
    {
        $this->consumers = $consumers;
    }

    /**
     * @return mixed
     */
    public function getConsumers()
    {
        return $this->consumers;
    }

    /**
     * @param array $array
     * @return Queue $this
     */
    public function populate(array $array)
    {
        $this->queueId = $array['queue_id'];
        $this->queueName = $array['queue_name'];
        $this->queueNtables = (int)$array['queue_ntables'];
        $this->queueCurTable = (int)$array['queue_cur_table'];
        $this->queueRotationPeriod = $array['queue_rotation_period'];
        $this->queueSwitchTime = new \DateTime($array['queue_switch_time']);
        $this->queueExternalTicker = (bool)$array['queue_external_ticker'];
        $this->queueTickerMaxCount = (int)$array['queue_ticker_max_count'];
        $this->queueTickerMaxLag = $array['queue_ticker_max_lag'];
        $this->queueTickerIdlePeriod = $array['queue_ticker_idle_period'];
        $this->queueDataPfx = $array['queue_data_pfx'];
        $this->queueEventSeq = $array['queue_event_seq'];
        $this->queueTickSeq = $array['queue_tick_seq'];

        return $this;
    }
}