<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use PgqManager\ConfigBundle\Entity\Database;
use Symfony\Component\Yaml\Parser;

class DatabaseManager
{
    /**
     * @var Database[]
     */
    private $databases;


    function __construct()
    {
        $config = array();
        $loader = new Parser();
        $this->databases = new ArrayCollection();

        if (file_exists(__DIR__ . '/../Resources/config/databases.yml')) {
            $configs = $loader->parse(file_get_contents(__DIR__ . '/../Resources/config/databases.yml'));
            $config = $configs['databases'];
        }

        foreach ($config as $data) {
            $db = new Database();
            $db->populate($data);
            $this->databases->add($db);
        }
    }

    /**
     * get database(s) by criteria
     *
     * @param array $criteria
     * @return Database|Database[]
     */
    public function getDatabase(array $criteria)
    {

        if (isset($criteria['id'])) {
            return $this->databases->get($criteria['id']);
        }

        $dbs = array();

        foreach ($this->databases as $database) {
            foreach ($criteria as $key => $value) {
                $func = 'get' . ucfirst($key);
                if (method_exists($database, $func) && $database->$func() === $value) {
                    $dbs[] = $database;
                    continue;
                }
            }
        }

        return $dbs;
    }

    /**
     * @param \PgqManager\ConfigBundle\Entity\Database[] $databases
     *
     * @return $this
     */
    public function setDatabases($databases)
    {
        if ($databases instanceof ArrayCollection)
            $this->databases = $databases;
        else
            $this->databases = new ArrayCollection($databases);

        return $this;
    }

    /**
     * @return \PgqManager\ConfigBundle\Entity\Database[]
     */
    public function getDatabases()
    {
        return $this->databases;
    }

    /**
     * @param Database $db
     * @return $this
     */
    public function addDatabase(Database $db)
    {
        $this->databases->add($db);

        return $this;
    }

    /**
     * @param Database $db
     * @return $this
     */
    public function removeDatabase(Database $db)
    {
        $this->databases->removeElement($db);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDatabases()
    {
        return $this->databases->count() > 0;
    }

    public function getAllDatabaseIds()
    {
        return array_keys($this->databases->toArray());
    }
}