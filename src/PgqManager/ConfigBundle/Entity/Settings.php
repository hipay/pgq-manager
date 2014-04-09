<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Settings
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


    public function to_Array()
    {
        $result = array();
        foreach ($this->databases as $database) {
            $result['databases'][] = $database->to_Array();
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
}
