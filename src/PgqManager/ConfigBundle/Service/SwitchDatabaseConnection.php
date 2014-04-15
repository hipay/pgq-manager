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
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

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
    public function __construct(Registry $doctrine, ConnectionFactory $dbFactory, SecurityContext $context)
    {
        $this->doctrine = $doctrine;
        $this->dbFactory = $dbFactory;
        $this->settings = $this->doctrine->getRepository('ConfigBundle:Settings')->findOneBy(
            array(
                'uid' => $context->getToken()->getUsername()
            )
        );
    }

    /**
     * @return array
     */
    public function getAllDatabaseIds($uid)
    {
        $databases = $this->doctrine->getRepository('ConfigBundle:Database')->findBy(
            array(
                'settings' => $this->settings
            )
        );
        $return = array();

        foreach ($databases as $db) {
            $return[] = $db->getId();
        }

        return $return;
    }

    /**
     * @param int $dbid
     * @return Connection
     */
    public function getDatabaseConnection($dbid)
    {
        $database = $this->doctrine->getRepository('ConfigBundle:Database')->findOneBy(
            array(
                'settings' => $this->settings,
                'id'       => $dbid
            )
        );

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