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
