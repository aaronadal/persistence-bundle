<?php

namespace Aaronadal\PersistenceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('aaronadal_persistence');
        // $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
