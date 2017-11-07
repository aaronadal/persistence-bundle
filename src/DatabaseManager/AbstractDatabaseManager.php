<?php

namespace Aaronadal\PersistenceBundle\DatabaseManager;


use Aaronadal\PersistenceBundle\Search\EmptySearchCriteria;
use Aaronadal\PersistenceBundle\Search\SearchCriteria;
use Aaronadal\PersistenceBundle\Search\SearchResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
abstract class AbstractDatabaseManager implements DatabaseManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var DatabaseManagerProviderInterface
     */
    private $provider;

    /**
     * {@inheritdoc}
     */
    public function getUniqueName()
    {
        return static::class;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvider(DatabaseManagerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Retrieves a database manager and returns it.
     *
     * @param string $name The unique name of the database manager to retrieve
     *
     * @return DatabaseManagerInterface
     */
    protected function getDatabaseManager($name)
    {
        return $this->provider->get($name);
    }

    /**
     * @param $repository
     *
     * @return EntityRepository
     */
    protected function getRepository($repository)
    {
        return $this->getEntityManager()->getRepository($repository);
    }

    /**
     * Creates a QueryBuilder.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * Gets a SearchResult from a QueryBuilder and a SearchCriteria.
     *
     * @param string              $select   Alias of the entity to SELECT and COUNT.
     * @param QueryBuilder        $qb       The QueryBuilder.
     * @param SearchCriteria|null $criteria The SearchCriteria.
     *
     * @return SearchResult
     */
    protected function getResult($select, QueryBuilder $qb, SearchCriteria $criteria = null)
    {
        $criteria = $criteria ?: new EmptySearchCriteria();

        $qb->select('COUNT(' . $select . ')');
        $count = $qb->getQuery()->getSingleScalarResult();

        $qb->select($select);
        $qb->setMaxResults($criteria->getLimit());
        $qb->setFirstResult($criteria->getOffset());
        $result = $qb->getQuery()->getResult();

        return new SearchResult($criteria, $result, $count);
    }

    /**
     * Gets one result or null from a QueryBuilder and a SearchCriteria.
     *
     * @param string              $select   Alias of the entity to SELECT.
     * @param QueryBuilder        $qb       The QueryBuilder.
     * @param SearchCriteria|null $criteria The SearchCriteria.
     *
     * @return mixed
     */
    protected function getOneResult($select, QueryBuilder $qb, SearchCriteria $criteria = null)
    {
        $qb->select($select);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets one single scalar result from a QueryBuilder and a SearchCriteria.
     *
     * @param string              $select   Alias de la tabla sobre la que se hará el SELECT.
     * @param QueryBuilder        $qb       The QueryBuilder.
     * @param SearchCriteria|null $criteria The SearchCriteria.
     *
     * @return mixed
     */
    protected function getOneScalarResult($select, QueryBuilder $qb, SearchCriteria $criteria = null)
    {
        $qb->select($select);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Generates a paginated SearchResult from an array.
     *
     * @param array               $entities An array of entities
     * @param SearchCriteria|null $criteria The SearchCriteria.
     *
     * @return SearchResult
     */
    protected function getResultFromArray($entities, SearchCriteria $criteria = null)
    {
        $criteria = $criteria ?: new EmptySearchCriteria();

        if($criteria->getLimit() === null) {
            return new SearchResult($criteria, $entities, count($entities));
        }

        $page  = [];
        $limit = $criteria->getOffset();
        $index = 0;
        foreach($entities as $entity) {
            if($index < $criteria->getOffset()) {
                $index++;
            }
            else {
                $page[] = $entity;
                $limit++;
            }

            if($limit >= $criteria->getLimit() * $criteria->getPage()) {
                break;
            }
        }

        return new SearchResult($criteria, $page, count($entities));
    }

    /**
     * Schedules an insert. It will be performed the next time flush will be called.
     *
     * @param mixed $entity Entity to insert
     */
    public function scheduleInsert($entity)
    {
        if($entity === null) {
            return;
        }

        $this->getEntityManager()->persist($entity);
    }

    /**
     * Inserts an entity.
     *
     * @param mixed $entity  Entity to insert
     * @param bool  $refresh If true, refresh will be called after the insert
     */
    public function insert($entity, $refresh = false)
    {
        $this->scheduleInsert($entity);
        $this->flush();

        if($refresh) {
            $this->refresh($entity);
        }
    }

    /**
     * Schedules a delete. It will be performed the next time flush will be called.
     *
     * @param mixed $entity Entity to delete
     */
    public function scheduleDelete($entity)
    {
        if($entity === null) {
            return;
        }

        $this->getEntityManager()->remove($entity);
    }

    /**
     * Deletes an entity.
     *
     * @param mixed $entity Entity to delete
     */
    public function delete($entity)
    {
        $this->scheduleDelete($entity);
        $this->flush();
    }

    /**
     * Refreshes the persistent state of an entity from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param mixed $entity The entity to refresh.
     */
    public function refresh($entity)
    {
        if($entity === null) {
            return;
        }

        $this->getEntityManager()->refresh($entity);
    }

    /**
     * Performs a flush and persists the changes.
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
