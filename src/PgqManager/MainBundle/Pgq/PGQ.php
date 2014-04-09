<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\MainBundle\Pgq;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
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
    public function  __construct(\Doctrine\DBAL\Connection $conn = null)
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
        $select = array('ev_id', 'ev_failed_time', 'ev_time', 'ev_retry', 'ev_type', 'ev_data', "'' as action");

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
        $sql = 'select * from pgq.get_queue_info(' . ($qname ? "'" . $qname . "'" : '') . ')';
        $return = array();

        $result = $this->dbal->executeQuery($sql)->fetchAll();

        foreach ($result as $row) {
            $queue = new Queue();
            $return[] = $queue->populate($row);
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
        $stmt = $this->dbal->executeQuery('select * from pgq.get_failed_queue_info('
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
            . 'FROM pgq.get_failed_queue_info() a    '
            . 'JOIN pgq.get_queue_info() b USING (queue_name) '
            . ($qname ? 'WHERE a.queue_name = "' . $qname . '"' : '')
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
        $sql = 'select * from pgq.get_consumer_info('
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
}