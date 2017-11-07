<?php

namespace Aaronadal\PersistenceBundle\DatabaseManager;

use Aaronadal\PersistenceBundle\Exception\DuplicatedDatabaseManagerException;
use Aaronadal\PersistenceBundle\Exception\MissingDatabaseManagerException;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class DatabaseManagerProvider implements DatabaseManagerProviderInterface
{

    private $managers = array();

    /**
     * {@inheritdoc}
     */
    public function add(DatabaseManagerInterface $manager)
    {
        $name = $manager->getUniqueName();
        if(array_key_exists($name, $this->managers)) {
            throw new DuplicatedDatabaseManagerException('Another database manager exists with this name: "' . $name . '"');
        }

        $this->managers[$name] = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if(!array_key_exists($name, $this->managers)) {
            throw new MissingDatabaseManagerException('No database manager found with this name: "' . $name . '"');
        }

        return $this->managers[$name];
    }
}
