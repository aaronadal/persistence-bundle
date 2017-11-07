<?php

namespace Aaronadal\PersistenceBundle\DatabaseManager;


use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
interface DatabaseManagerInterface
{

    /**
     * Returns the unique name of this database manager.
     *
     * @return string
     */
    public function getUniqueName();

    /**
     * Sets the Doctrine entity manager.
     *
     * @param EntityManagerInterface $manager
     */
    public function setEntityManager(EntityManagerInterface $manager);

    /**
     * Sets the database manager provider.
     *
     * @param DatabaseManagerProviderInterface $provider
     */
    public function setProvider(DatabaseManagerProviderInterface $provider);

}
