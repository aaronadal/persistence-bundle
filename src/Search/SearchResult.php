<?php

namespace Aaronadal\PersistenceBundle\Search;


use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class SearchResult implements Countable, IteratorAggregate, ArrayAccess
{

    private $criteria;
    private $elements;
    private $totalCount;

    /**
     * Creates a new SearchResult instance.
     *
     * @param SearchCriteria $criteria
     * @param array          $elements
     * @param int            $totalCount
     */
    function __construct(SearchCriteria $criteria, $elements, $totalCount)
    {
        $this->criteria   = $criteria;
        $this->elements   = $elements;
        $this->totalCount = intval($totalCount);
    }

    /**
     * Devuelve el criterio de búsqueda empleado en la consulta realizada.
     *
     * @return SearchCriteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Devuelve los elementos obtenidos de la consulta realizada.
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function getFirstElementNumber()
    {
        return ($this->getCurrentPage() - 1) * $this->getCriteria()->getLimit() + 1;
    }

    public function getLastElementNumber()
    {
        return min($this->getFirstElementNumber() + $this->getCriteria()->getLimit() - 1, $this->getTotalCount());
    }

    /**
     * Devuelve el número total de entradas para la consulta realizada.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Devuelve la página actual.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getCriteria()->getPage();
    }

    /**
     * Devuelve la última página del resultado.
     *
     * @return int
     */
    public function getLastPage()
    {

        $limit = $this->getCriteria()->getLimit();
        $total = $this->getTotalCount();

        if(!$limit || $limit < 0) {
            return 1;
        }

        return ceil($total / $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->getElements());
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getElements());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->getElements()[$offset]) || array_key_exists($offset, $this->getElements());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->getElements()[$offset]) ? $this->getElements()[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if(!isset($offset)) {
            return $this->getElements()[] = $value;
        }

        return $this->getElements()[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if(!$this->offsetExists($offset)) {
            return null;
        }

        $removed = $this->getElements()[$offset];
        unset($this->getElements()[$offset]);

        return $removed;
    }
}
