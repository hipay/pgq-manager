<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\MainBundle\Controller;


use Doctrine\DBAL\DBALException;
use PgqManager\MainBundle\Pgq\PGQ;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FailedEventController extends Controller
{
    /**
     * @Template()
     * @param int $id
     * @return array
     */
    public function indexAction($id = null, $queue = null, $consumer = null, $event = null)
    {
        $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
        if ($id && !is_numeric($id)) {
            $id = $switchDbService->getDatabase(array(
                'name' => $id,
                'uid'  => $this->get('security.context')->getToken()->getUsername()
            ))->getId();
        }
        $dbIds = $switchDbService->getAllDatabaseIds($this->get('security.context')->getToken()->getUsername());
        $pgq = new PGQ();
        $consumers = array();
        $queues = array();
        $failedDatabases = array();
        $database = null;

        if ($id != null) {
            $dbIds = array_intersect($dbIds, array($id));
        }

        foreach ($dbIds as $dbId) {
            $dbal = $switchDbService->getDatabaseConnection($dbId);
            $pgq->init($dbal, $this->getDoctrine()->getManager());

            $queues[$dbId] = $pgq->getQueueInfo(null);
            $consumers[$dbId] = $pgq->getConsumerInfo();
            try {
                $failedDatabases[$dbId] = $pgq->getFailedQueueInfo(null, null, true);
            } catch (DBALException $e) {
                $failedDatabases[$dbId]['error'] = 'Function pgq.get_failed_queue_info() not installed in database';
            }
        }

        $result = array(
            'dbqueues'        => $queues,
            'dbconsumers'     => $consumers,
            'failedDatabases' => $failedDatabases
        );

        if ($queue && $consumer) {
            $result['filter'] = array(
                'queue'    => $queue,
                'consumer' => $consumer
            );
            if($event) {
                $result['filter']['event'] = $event;
            }
        }
        return $result;
    }

    public function listAjaxAction()
    {
        // check mandatory parameters
        if (null !== $this->get('request')->get('iDisplayLength')
            && null !== $this->get('request')->get('iDisplayStart')
            && null !== $this->get('request')->get('database')
            && null !== $this->get('request')->get('queue')
            && null !== $this->get('request')->get('consumer')
        ) {
            $displayStart = $this->get('request')->get('iDisplayStart');
            // save paginate display start
            if (!is_numeric($displayStart) || $displayStart < 0) {
                $displayStart = 0;
            }


            // start and offset parameter
            $criteria['iDisplayLength'] = $this->get('request')->get('iDisplayLength');
            $criteria['iDisplayStart'] = $displayStart;

            $criteria['queue'] = $this->get('request')->get('queue');
            $criteria['consumer'] = $this->get('request')->get('consumer');

            // eventid specified?
            if ($this->get('request')->get('eventid')) {
                $criteria['event_id'] = $this->get('request')->get('eventid');
            }

            // search parameter
            if ($this->get('request')->get('sSearch')) {
                $criteria['sSearch'] = $this->get('request')->get('sSearch');
            }

            // sorting ...
            if ($this->get('request')->get('iSortCol_0')) {
                if ('0' == $this->get('request')->get('iSortCol_0')) {
                    $criteria['sort'] = 'ev_id ' . $this->get('request')->get('sSortDir_0');
                }
                if ('1' == $this->get('request')->get('iSortCol_0')) {
                    $criteria['sort'] = 'ev_failed_time ' . $this->get('request')->get('sSortDir_0');
                }
                if ('2' == $this->get('request')->get('iSortCol_0')) {
                    $criteria['sort'] = 'ev_time ' . $this->get('request')->get('sSortDir_0');
                }
            }

            // execute query
            $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
            $dbal = $switchDbService->getDatabaseConnection((int)$this->get('request')->get('database'));
            $pgq = new PGQ();
            $pgq->init($dbal, $this->getDoctrine()->getManager());

            $results = $pgq->searchFailedEvent($criteria);


            return new Response(json_encode(array(
                'aaData'               => $results['records'],
                'sEcho'                => (int)$this->get('request')->get('sEcho') + 1,
                'iTotalDisplayRecords' => $results['count'],
                'iTotalRecords'        => $results['totalCount']
            )));
        } else {
            return new Response(json_encode(array(
                'aaData'               => array(),
                'sEcho'                => (int)$this->get('request')->get('sEcho') + 1,
                'iTotalDisplayRecords' => 0,
                'iTotalRecords'        => 0
            )));
        }
    }


    public function doAction()
    {
        $return = array();

        // check mandatory parameters
        if (
            null !== $this->get('request')->get('database')
            && null !== $this->get('request')->get('queue')
            && null !== $this->get('request')->get('consumer')
            && null !== $this->get('request')->get('event')
            && null !== $this->get('request')->get('action')
        ) {
            $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
            $dbal = $switchDbService->getDatabaseConnection((int)$this->get('request')->get('database'));
            $pgq = new PGQ();
            $pgq->init($dbal, $this->getDoctrine()->getManager());
            try {

                if (in_array($this->get('request')->get('action'), array('edit'))) {
                    throw new \Exception('Action ' . strtoupper($this->get('request')->get('action')) . ' not implemented yet');
                }

                $method = 'failedEvent' . ucfirst($this->get('request')->get('action'));
                if (!method_exists($pgq, $method)) {
                    throw new \Exception('Invalid action');
                }
                $dbal->beginTransaction();

                $pgq->$method(
                    $this->get('request')->get('queue'),
                    $this->get('request')->get('consumer'),
                    $this->get('request')->get('event')
                );

                $dbal->commit();

                $message =
                    'Action ' . $this->get('request')->get('action')
                    . ' for event ' . $this->get('request')->get('event')
                    . ' successfully executed';
                $class = 'success';

            } catch (DBALException $e) {
                $dbal->rollBack();
                $message = $e->getMessage();
                $class = 'danger';
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $class = 'danger';
            }
        } else {
            $class = 'danger';
            $message = 'Some missing parameters';
        }

        $response = new JsonResponse();
        $response->setData(array(
            'flashmessage' => array(
                'message' => $message,
                'class'   => $class
            )
        ));

        return $response;
    }
}