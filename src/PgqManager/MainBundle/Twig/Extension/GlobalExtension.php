<?php

namespace PgqManager\MainBundle\Twig\Extension;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PgqManager\ConfigBundle\Entity\Database;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class GlobalExtension extends \Twig_Extension
{
    protected $databases;

    protected $em;

    public function __construct(EntityManager $em, SecurityContext $context)
    {
        $databases = new ArrayCollection();
        $settings = null;

        if ($context->getToken()) {
            $settings = $em->getRepository('ConfigBundle:Settings')->findOneBy(array(
                'uid' => $context->getToken()->getUsername()
            ));
        }

        if ($settings) {
            $databases = $settings->getDatabases();
        }

        $this->databases = $databases;

        $this->em = $em;
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
        foreach ($this->databases as $database) {

            if (!is_numeric($id) && $database->getName() == $id) {
                return $database;
            }

            if ($database->getId() == $id) {
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
