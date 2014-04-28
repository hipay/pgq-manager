<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Service;


use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use PgqManager\ConfigBundle\Entity\Database;
use PgqManager\ConfigBundle\Entity\Settings;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;

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
     * @var Settings
     */
    private $settings;

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