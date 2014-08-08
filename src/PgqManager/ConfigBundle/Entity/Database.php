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

namespace PgqManager\ConfigBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use \PgqManager\ConfigBundle\Validator\Constraints\DBAL;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Database
 *
 * @ORM\Entity(repositoryClass="PgqManager\ConfigBundle\Repository\DatabaseRepository")
 * @ORM\Table(name="fscommon.pgqm_database")
 * @DBAL
 * @package PgqManager\ConfigBundle\Entity
 */
class Database
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Database Driver
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(callback="getDriverKeys")
     *
     * @var string $driver
     */
    private $driver;

    /**
     * Database Host
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @var string $host
     */
    private $host;

    /**
     * Database Port
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min = "0")
     *
     * @var string $port
     */
    private $port;

    /**
     * Database Name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @var string $name
     */
    private $name;

    /**
     * Display Name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @var string $displayName
     */
    private $displayName;

    /**
     * Database User
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     *
     * @var string $user
     */
    private $user;

    /**
     * Database Password
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string $password
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="Settings", inversedBy="databases")
     * @ORM\JoinColumn(name="settings_id", referencedColumnName="id")
     */
    //protected $settings;

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public static function getDriverKeys()
    {
        return array_keys(static::getDrivers());
    }

    /**
     * @return array
     */
    public static function getDrivers()
    {
        return array(
            'pdo_mysql'  => 'MySQL (PDO)',
            'pdo_sqlite' => 'SQLite (PDO)',
            'pdo_pgsql'  => 'PosgreSQL (PDO)',
            'oci8'       => 'Oracle (native)',
            'ibm_db2'    => 'IBM DB2 (native)',
            'pdo_oci'    => 'Oracle (PDO)',
            'pdo_ibm'    => 'IBM DB2 (PDO)',
            'pdo_sqlsrv' => 'SQLServer (PDO)',
        );
    }

    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            $key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            if (property_exists($this, lcfirst($key))) {
                $setter = 'set' . $key;

                if ($setter === 'setDriver' && !in_array($value, $this->getDriverKeys()))
                    throw new \InvalidArgumentException('Invalid argument for driver database "' . $value . '" must be part of ' . var_export($this->getDriverKeys()));

                $this->$setter($value);
            }
        }
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function toArray()
    {
        return array(
            'id'           => $this->getId(),
            'display_name' => $this->getDisplayName(),
            'driver'       => $this->getDriver(),
            'host'         => $this->getHost(),
            'port'         => $this->getPort(),
            'name'         => $this->getName(),
            'user'         => $this->getUser(),
            'password'     => $this->getPassword()
        );
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set setting
     *
     * @param \PgqManager\ConfigBundle\Entity\Settings $setting
     * @return Database
     */
    public function setSettings(\PgqManager\ConfigBundle\Entity\Settings $setting = null)
    {
        $this->settings = $setting;

        return $this;
    }

    /**
     * Get setting
     *
     * @return \PgqManager\ConfigBundle\Entity\Settings
     */
   // public function getSettings()
   // {
   //     return $this->settings;
   // }
}
