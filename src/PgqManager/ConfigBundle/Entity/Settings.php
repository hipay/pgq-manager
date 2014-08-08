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


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Settings
 *
 * @ORM\Entity
 * @ORM\Table(name="pgqm_settings")
 * @package PgqManager\ConfigBundle\Entity
 */
class Settings
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Databases
     *
     * @Assert\Count(min=1, minMessage="At least one database must be configured")
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Database", mappedBy="settings", cascade={"persist", "merge", "remove"})
     *
     * @var ArrayCollection|Database[]
     */
    protected $databases;

    /**
     *
     * @ORM\Column(type="string")
     *
     * @var integer $uid
     */
    protected $uid;

    public function __construct()
    {
        $this->databases = new ArrayCollection();
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }


    public function toArray()
    {
        $result = array();
        foreach ($this->databases as $database) {
            $result['databases'][] = $database->toArray();
        }
        return $result;
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
     * Add databases
     *
     * @param \PgqManager\ConfigBundle\Entity\Database $databases
     * @return Settings
     */
    public function addDatabase(\PgqManager\ConfigBundle\Entity\Database $databases)
    {
        $databases->setSettings($this);
        $this->databases[] = $databases;

        return $this;
    }

    /**
     * Remove databases
     *
     * @param \PgqManager\ConfigBundle\Entity\Database $databases
     */
    public function removeDatabase(\PgqManager\ConfigBundle\Entity\Database $databases)
    {
        $databases->setSettings(null);

        $this->databases->removeElement($databases);
    }

    /**
     * Get databases
     *
     * @return Database[]
     */
    public function getDatabases()
    {
        return $this->databases;
    }

    /**
     * Set databases
     *
     * @param Database[]
     */
    public function setDatabases(ArrayCollection $dbs)
    {
        $this->databases = $dbs;
    }
}
