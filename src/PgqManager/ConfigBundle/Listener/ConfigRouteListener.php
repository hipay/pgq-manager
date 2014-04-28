<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Listener;


use Doctrine\ORM\EntityManager;
use PgqManager\ConfigBundle\Service\DatabaseManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;

class ConfigRouteListener
{
    /**
     * @var DatabaseManager
     */
    private $dm;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $security;

    public function __construct(SecurityContext $security, DatabaseManager $dm, Router $router)
    {
        $this->security = $security;
        $this->router = $router;
        $this->dm = $dm;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->security->getToken()
            || HttpKernel::MASTER_REQUEST != $event->getRequestType()
            || preg_match('/^_config_/', $event->getRequest()->get('_route'))
        ) {
            // ne rien faire si ce n'est pas la requête principale
            return;
        }

        if (!$this->dm->hasDatabases()) {
            $url = $this->router->generate('_config_home');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}