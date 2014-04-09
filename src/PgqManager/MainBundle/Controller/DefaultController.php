<?php

namespace PgqManager\MainBundle\Controller;

use PgqManager\MainBundle\Pgq\PGQ;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Collect queue info on a database, return result in JSON format
     *
     * @return Response
     */
    public function queueInfoAction()
    {

        list($dbId, $qname) = explode('/', $this->get('request')->get('id'));
        $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
        $pgq = new PGQ();
        $pgq->init($switchDbService->getDatabaseConnection($dbId), $this->getDoctrine()->getManager());
        $queues = $pgq->getQueueInfo($qname);

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($queues[0], 'json'));
    }


    /**
     * Collect consumer info on a database, return result in JSON format
     *
     * @return Response
     */
    public function consumerInfoAction()
    {

        list($dbId, $qname, $cname) = explode('/', $this->get('request')->get('id'));
        $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
        $pgq = new PGQ();
        $pgq->init($switchDbService->getDatabaseConnection($dbId), $this->getDoctrine()->getManager());
        $consumers = $pgq->getConsumerInfo($qname, $cname);

        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($consumers[0], 'json'));
    }
}
