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
namespace PgqManager\MainBundle\Twig\Extension;

use PgqManager\ConfigBundle\Service\DatabaseManager;
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
