<?php

namespace PgqManager\MainBundle\Twig\Extension;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PgqManager\ConfigBundle\Entity\Database;
use PgqManager\ConfigBundle\Service\DatabaseManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class GlobalExtension extends \Twig_Extension
{
    protected $databases;

    protected $dm;

    public function __construct(DatabaseManager $dm, SecurityContext $context)
    {
        $this->databases = $dm->getDatabases();

        $this->dm = $dm;
    }

    public function getGlobals()
    {

        return array(
            'ConfigDatabases' => $this->databases
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('databaseInfos', array($this, 'databaseInfos')),
        );
    }

    public function databaseInfos($id)
    {
        if (is_numeric($id)) {
            return $this->databases[$id];
        }

        foreach ($this->databases as $database) {
            if (!is_numeric($id) && $database->getName() == $id) {
                return $database;
            }
        }

        return null;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'global_extension';
    }
}
