<?php

namespace Aaronadal\PersistenceBundle;

use Aaronadal\PersistenceBundle\DependencyInjection\DatabaseManagerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AaronadalPersistenceBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DatabaseManagerCompilerPass());
    }

}
