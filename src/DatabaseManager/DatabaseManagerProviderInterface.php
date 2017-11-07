<?php

namespace Aaronadal\PersistenceBundle\DatabaseManager;


use Aaronadal\PersistenceBundle\Exception\DuplicatedDatabaseManagerException;
use Aaronadal\PersistenceBundle\Exception\MissingDatabaseManagerException;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
interface DatabaseManagerProviderInterface
{

    /**
     * Registers a new database manager and makes it available.
     *
     * @param DatabaseManagerInterface $databaseManager The database manager to add
     *
     * @throws DuplicatedDatabaseManagerException If another database manager is registered with this name.
     */
    public function add(DatabaseManagerInterface $databaseManager);

    /**
     * Retrieves a database manager and returns it.
     *
     * @param string $name The unique name of the database manager to retrieve
     *
     * @return DatabaseManagerInterface The database manager
     *
     * @throws MissingDatabaseManagerException If no database manager is registered with this name.
     */
    public function get($name);

}
