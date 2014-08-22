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

namespace PgqManager\ConfigBundle\Listener;

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