<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class DatabaseCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $loader = new Parser();
        if (file_exists(__DIR__ . '/../../Resources/config/databases.yml')) {
            $configs = $loader->parse(file_get_contents(__DIR__ . '/../../Resources/config/databases.yml'));
            $container->setParameter('config_database.databases', $configs['parameters']['config']['databases']);
        }

    }

}