<?php

namespace Aaronadal\PersistenceBundle\DependencyInjection;


use Aaronadal\PersistenceBundle\DatabaseManager\DatabaseManagerInterface;
use Aaronadal\PersistenceBundle\Exception\InvalidDatabaseManagerException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class DatabaseManagerCompilerPass implements CompilerPassInterface
{

    const TAG_NAME = 'aaronadal.persistence.database_manager';

    public function process(ContainerBuilder $container)
    {
        if(!$container->has('aaronadal.persistence.database_manager.provider')) {
            return;
        }

        $providerDefinition = $container->findDefinition('database_manager.provider');
        $taggedServices     = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach($taggedServices as $id => $attributes) {
            $managerDefinition = $container->findDefinition($id);
            $definitionClass   = $managerDefinition->getClass();
            if(!is_subclass_of($definitionClass, DatabaseManagerInterface::class)) {
                throw new InvalidDatabaseManagerException(
                    'All database managers must implement DatabaseManagerInterface'
                );
            }

            $entityManagerName = $attributes[0]['em'] ?? null;
            if($entityManagerName === null) {
                $entityManagerReference = 'doctrine.orm.entity_manager';
            }
            else {
                $entityManagerReference = 'doctrine.orm.' . $entityManagerName . '_entity_manager';
            }

            if(!$container->has($entityManagerReference)) {
                throw new \RuntimeException(
                    'The "' . $entityManagerName ?: '<default>' . '" entity manager is not defined.'
                );
            }

            $managerDefinition->addMethodCall('setEntityManager', [new Reference($entityManagerReference)]);
            $managerDefinition->addMethodCall('setProvider', [new Reference('database_manager.provider')]);
            $providerDefinition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
