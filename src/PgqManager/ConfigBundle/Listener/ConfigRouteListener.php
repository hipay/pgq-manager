<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Listener;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;

class ConfigRouteListener
{

    private $em;
    private $router;
    private $security;

    public function __construct(SecurityContext $security, EntityManager $em, Router $router)
    {
        $this->security = $security;
        $this->router = $router;
        $this->em = $em;
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

        $settings = $this->em->getRepository('ConfigBundle:Settings')->findBy(array(
            'uid' => $this->security->getToken()->getUsername()
        ));

        if (!$settings) {
            $url = $this->router->generate('_config_home');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}