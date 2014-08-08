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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class QueueController extends Controller
{
    /**
     * @Template()
     * @param int $id Database Id
     * @return array
     * @throws \Symfony\Component\Intl\Exception\NotImplementedException
     */
    public function indexAction($id, Request $request)
    {
        $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
        $databaseManager = $this->container->get('pgq_config_bundle.db_manager');

        $dbal = $switchDbService->getDatabaseConnection((int)$this->get('request')->get('database'));
        $pgq = new PGQ();
        $pgq->init($dbal, $this->getDoctrine()->getManager());

        $form = $this->createFormBuilder()
            ->add('name', 'text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $qname = $form->get('name')->getData();
            $result = $pgq->createQueue($qname);
            $flash = $this->get('braincrafted_bootstrap.flash');
            if (!$result)
                $flash->error('Queue '. $qname . ' already exist');
            else
                $flash->error('Queue '. $qname . ' successfully created');
        }

        $queues = $pgq->getQueueInfo();

        return array('queues' => $queues, 'formNewQueue'=>$form->createView());
    }
}
