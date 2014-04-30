<?php

namespace PgqManager\MainBundle\Controller;

use Doctrine\DBAL\DBALException;
use PgqManager\MainBundle\Pgq\PGQ;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Intl\Exception\NotImplementedException;

class QueueController extends Controller
{
    public function indexAction($id)
    {
        $switchDbService = $this->container->get('pgq_config_bundle.switch_db');
        $databaseManager = $this->container->get('pgq_config_bundle.db_manager');

        throw new NotImplementedException('Coming soon...');
    }
}
