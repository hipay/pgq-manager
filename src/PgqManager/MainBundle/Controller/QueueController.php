<?php

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
        throw new NotImplementedException('Coming soon...');
    }
}
