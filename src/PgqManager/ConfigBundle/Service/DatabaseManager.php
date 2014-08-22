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