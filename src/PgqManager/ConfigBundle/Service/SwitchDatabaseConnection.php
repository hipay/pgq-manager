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

namespace PgqManager\ConfigBundle\Service;


use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use PgqManager\ConfigBundle\Entity\Database;
use PgqManager\ConfigBundle\Entity\Settings;
use Symfony\Component\DependencyInjection\ContainerAware;

class SwitchDatabaseConnection extends ContainerAware
{

    /**
     * @var DatabaseManager
     */
    private $dm;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\ConnectionFactory
     */
    private $dbFactory;

    /**
     * @param Registry $doctrine
     * @param ConnectionFactory $dbFactory
     */
    public function __construct(DatabaseManager $dm, ConnectionFactory $dbFactory)
    {
        $this->dm = $dm;
        $this->dbFactory = $dbFactory;
    }

    /**
     * @param int $dbid
     * @return Connection
     */
    public function getDatabaseConnection($dbid)
    {
        $database = $this->dm->getDatabase(array('id' => $dbid));

        return $this->dbFactory->createConnection(array(
            'driver'   => $database->getDriver(),
            'user'     => $database->getUser(),
            'password' => $database->getPassword(),
            'host'     => $database->getHost(),
            'dbname'   => $database->getName(),
            'port'     => $database->getPort(),
        ));
    }

    /**
     * @param array $criteria
     * @return Database
     */
    public function getDatabase(array $criteria)
    {
        if (isset($criteria['id']))
            $database = $this->doctrine->getRepository('ConfigBundle:Database')->find($criteria['id']);
        else
            $database = $this->doctrine->getRepository('ConfigBundle:Database')->findOne($criteria);


        return $database;
    }
}