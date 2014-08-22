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

namespace PgqManager\MainBundle\Pgq;


use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use PgqManager\MainBundle\Entity\Consumer;
use PgqManager\MainBundle\Entity\Queue;

class PGQ
{
    /**
     * @var Connection
     */
    private $dbal;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param Connection $dbal
     * @param EntityManager $em
     */
    public function init(Connection $dbal, EntityManager $em)
    {
        $this->dbal = $dbal;
        $this->em = EntityManager::create($dbal, $em->getConfiguration());
    }

    /**
     * @param Connection $conn
     */
    public function  __construct(Connection $conn = null)
    {
        $this->dbal = $conn;
    }

    /**
     * Search for failed event depending on a queue and a consumer
     *
     * @param $criteria
     * @return array
     */
    public function searchFailedEvent($criteria)
    {
        $result = array();
        $select = array('ev_id', 'ev_failed_time', 'ev_time', 'ev_retry', 'ev_type', 'ev_data', 'ev_failed_reason', "'' as action");

        $sqlbody = " FROM pgq.failed_queue fq, pgq.consumer,"
            . " pgq.queue, pgq.subscription"
            . " WHERE queue_name = '" . $criteria['queue'] . "'"
            . " AND co_name = '" . $criteria['consumer'] . "'"
            . " AND sub_consumer = co_id"
            . " AND sub_queue = queue_id"
            . " AND ev_owner = sub_id";

        if (isset($criteria['sSearch'])) {
            $sqlbody .= ' AND ( '
                . (is_numeric($criteria['sSearch']) ? 'ev_id = ' . $criteria['sSearch'] : '0 = 1')
                . " OR UPPER(ev_type) LIKE '%" . strtoupper($criteria['sSearch']) . "%'"
                . " OR UPPER(ev_data) LIKE '%" . strtoupper($criteria['sSearch']) . "%'"
                . ")";
        }

        if (isset($criteria['event_id'])) {
            $sqlbody .= " AND ev_id = " . $criteria['event_id'];
        }

        if (isset($criteria['sort'])) {
            $sqlorder =
                ' ORDER BY ' . $criteria['sort'];
        } else {
            $sqlorder = '';
        }

        if (isset($criteria['iDisplayLength']) && isset($criteria['iDisplayStart'])) {
            $sqllimit =
                ' LIMIT ' . $criteria['iDisplayLength']
                . ' OFFSET ' . $criteria['iDisplayStart'];
        } else {
            $sqllimit = '';
        }

        $sql = 'SELECT ' . implode(', ', $select) . $sqlbody . $sqlorder . $sqllimit;
        $result['records'] = $this->dbal->executeQuery($sql)->fetchAll();

        $select = array('count(*)');
        $sql = 'SELECT ' . implode(', ', $select) . $sqlbody;
        $result['count'] = $this->dbal->executeQuery($sql)->fetchAll();
        $result['count'] = $result['count'][0]['count'];

        $sql = "SELECT * from pgq.failed_event_count('" . $criteria["queue"] . "', '" . $criteria['consumer'] . "')";
        $result['totalCount'] = $this->dbal->executeQuery($sql)->fetchAll();
        $result['totalCount'] = $result['totalCount'][0]['failed_event_count'];

        return $result;
    }

    /**
     * Get information about a(ll) queue(s)
     *
     * @param null|string $qname
     * @return Queue[]
     */
    public function getQueueInfo($qname = null)
    {
        $sql = 'select *'
            . ' from pgq.queue' . ($qname ? " WHERE queue_name = '" . $qname . "'" : '');
        $return = array();

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        foreach ($result as $row) {
            $queue = new Queue();
            $queue->populate($row);
            $queue->setQueueRetryCount($this->get_retry_event_count($queue));
            $queue->setQueueFailedCount($this->get_failed_event_count($queue));
            $queue->setQueueCount($this->get_event_count($queue));
            $queue->setConsumers($this->getConsumerInfo($queue->getQueueName()));
            $return[] = $queue;
        }

        return $return;
    }

    /**
     * Get information about a(ll) queue(s) which has failed events
     *
     * @param null|string $qname
     * @param null|string $cname
     * @param bool $onlyFailed
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function getFailedQueueInfo($qname = null, $cname = null, $onlyFailed = false)
    {
        $stmt = $this->dbal->executeQuery('select * from pgq . get_failed_queue_info('
            . ($qname ? '"' . $qname . '"' : '')
            . ($cname ? ', "' . $cname . '"' : '')
            . ')'
            . ($onlyFailed ? ' WHERE failed_events > 0' : ''));

        return $stmt;
    }

    /*
    public function getAllQueueInfo($qname = null)
    {
        $stmt = $this->dbal->executeQuery(
            'SELECT * '
            . 'FROM pgq . get_failed_queue_info() a    '
            . 'JOIN pgq . get_queue_info() b USING(queue_name) '
            . ($qname ? 'WHERE a . queue_name = "' . $qname . '"' : '')
        );

        return $stmt;
    }*/

    /**
     * Get information about a(ll) consumer(s)
     *
     * @param null|string $qname
     * @param null|string $cname
     * @return Consumer[]
     */
    public function getConsumerInfo($qname = null, $cname = null)
    {
        $sql = 'select * from pgq . get_consumer_info('
            . ($qname ? "'" . $qname . "'" : '')
            . ($cname ? ", '" . $cname . "'" : '')
            . ')';
        $return = array();

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        foreach ($result as $row) {
            $consumer = new Consumer();
            $return[] = $consumer->populate($row);
        }

        return $return;
    }

    /**
     * Place a failed event on retry status
     *
     * @param string $qname
     * @param string $cname
     * @param int $event
     * @param string $data
     *
     * @return array
     */
    public function failedEventRetry($qname, $cname, $event)
    {
        $sql =
            "SELECT * FROM pgq.failed_event_retry("
            . "'" . $qname . "'"
            . ", '" . $cname . "'"
            . ", " . $event . ")";

        return $this->dbal->executeQuery($sql)->fetchAll();
    }

    /**
     * Place a failed event on delete status
     *
     * @param string $qname
     * @param string $cname
     * @param int $event
     * @param string $data
     *
     * @return array
     */
    public function failedEventDelete($qname, $cname, $event)
    {
        $sql =
            "SELECT * FROM pgq.failed_event_delete("
            . "'" . $qname . "'"
            . ", '" . $cname . "'"
            . ", " . $event . ")";

        return $this->dbal->executeQuery($sql)->fetchAll();
    }

    /**
     * @param string $qname
     * @param string $cname
     * @param int $event
     * @param string|null $data
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function failedEventEdit($qname, $cname, $event, $data = null)
    {
        $sql =
            "select * from pgq.failed_event_list('" . $qname . "','" . $cname . "') where ev_id = " . $event;

        if ($this->dbal->executeQuery($sql)->rowCount() === 1) {
            $event = $this->dbal->executeQuery($sql)->fetchAll();
            $event = $event[0];
            $insert =
                "select * from pgq.insert_event("
                . "'" . $qname . "'"
                . ", '" . $event['ev_type'] . "'"
                . ", '" . $data . "'"
                . ", '" . $event['ev_extra1'] . "'"
                . ", '" . $event['ev_extra2'] . "'"
                . ", '" . $event['ev_extra3'] . "'"
                . ", '" . $event['ev_extra4'] . "'"
                . ")";

            $result = $this->dbal->executeQuery($insert)->fetchAll();
            $this->failedEventDelete($qname, $cname, $event['ev_id']);

            return $result[0]['insert_event'];
        }

        throw new NonUniqueResultException('No event found or too many');
    }


    public function get_failed_event_count(Queue $queue)
    {
        $sql =
            'SELECT'
            . ' count(fq.*) as count'
            . ' FROM pgq.failed_queue fq'
            . ' inner join pgq.subscription s on fq.ev_owner = s.sub_id'
            . ' inner join pgq.queue q on s.sub_queue = q.queue_id'
            . ' WHERE q.queue_id = ' . $queue->getQueueId();
        $result = $this->dbal->executeQuery($sql)->fetchAll();

        return $result[0]['count'];
    }

    public function get_retry_event_count(Queue $queue)
    {
        $sql =
            'select count(*) as count'
            . ' from ' . $queue->getQueueDataPfx()
            . ' where ev_retry is not null';

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        return $result[0]['count'];
    }

    public function get_event_count(Queue $queue)
    {
        $sql =
            'select count(*) as count'
            . ' from ' . $queue->getQueueDataPfx();

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        return $result[0]['count'];
    }

    public function createQueue($qname)
    {
        $sql =
            'select *'
            . ' from pgq.create_queue(\'' . $qname . '\')';

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        return $result[0];
    }
}