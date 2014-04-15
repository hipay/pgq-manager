<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\MainBundle\Entity;

/**
 * Class Consumer
 * @package PgqManager\MainBundle\Entity
 */
class Consumer
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $consumerName;

    /**
     * @var string
     */
    private $lag;

    /**
     * @var string
     */
    private $lastSeen;

    /**
     * @var int
     */
    private $lastTick;

    /**
     * @var int
     */
    private $currentBatch;

    /**
     * @var int
     */
    private $nextTick;

    /**
     * @param string $consumerName
     */
    public function setConsumerName($consumerName)
    {
        $this->consumerName = $consumerName;
    }

    /**
     * @return string
     */
    public function getConsumerName()
    {
        return $this->consumerName;
    }

    /**
     * @param int $currentBatch
     */
    public function setCurrentBatch($currentBatch)
    {
        $this->currentBatch = $currentBatch;
    }

    /**
     * @return int
     */
    public function getCurrentBatch()
    {
        return $this->currentBatch;
    }

    /**
     * @param string $lag
     */
    public function setLag($lag)
    {
        $this->lag = $lag;
    }

    /**
     * @return string
     */
    public function getLag()
    {
        return $this->lag;
    }

    /**
     * @param string $lastSeen
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }

    /**
     * @return string
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @param int $lastTick
     */
    public function setLastTick($lastTick)
    {
        $this->lastTick = $lastTick;
    }

    /**
     * @return int
     */
    public function getLastTick()
    {
        return $this->lastTick;
    }

    /**
     * @param int $nextTick
     */
    public function setNextTick($nextTick)
    {
        $this->nextTick = $nextTick;
    }

    /**
     * @return int
     */
    public function getNextTick()
    {
        return $this->nextTick;
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
     * @param array $array
     * @return Consumer $this
     */
    public function populate(array $array)
    {
        $this->queueName = $array['queue_name'];
        $this->consumerName = $array['consumer_name'];
        $this->lag = $array['lag'];
        $this->lastSeen = new \DateTime($array['last_seen']);
        $this->lastTick = (int)$array['last_tick'];
        $this->currentBatch = (int)$array['current_batch'];
        $this->nextTick = (int)$array['next_tick'];

        return $this;
    }

}