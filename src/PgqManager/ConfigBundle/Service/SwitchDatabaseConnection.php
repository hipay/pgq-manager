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
use Symfony\Component\DependencyInjection\ContainerAware;

class SwitchDatabaseConnection extends ContainerAware
{

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\ConnectionFactory
     */
    private $dbFactory;

    /**
     * @param Registry $doctrine
     * @param ConnectionFactory $dbFactory
     */
    public function __construct(Registry $doctrine, ConnectionFactory $dbFactory)
    {
        $this->doctrine = $doctrine;
        $this->dbFactory = $dbFactory;
    }

    /**
     * @param $dbid
     * @return Connection
     */
    public function getDatabaseConnection($dbid)
    {
        $database = $this->doctrine->getRepository('ConfigBundle:Database')->find($dbid);

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
     * @return array
     */
    public function getAllDatabaseIds()
    {
        $databases = $this->doctrine->getRepository('ConfigBundle:Database')->findAll();
        $return = array();

        foreach ($databases as $db) {
            $return[] = $db->getId();
        }

        return $return;
    }
}