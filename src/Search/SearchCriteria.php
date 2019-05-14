<?php

namespace Aaronadal\PersistenceBundle\Search;


use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class SearchCriteria implements Countable, IteratorAggregate, ArrayAccess
{

    const UNLIMITED = null;

    private $page;
    private $limit;
    private $elements;

    /**
     * Creates a new SearchCriteria instance.
     *
     * @param int      $page     Página de la búsqueda.
     * @param array    $criteria Criterios de búsqueda.
     * @param int|null $limit    Límite de elementos por página.
     */
    function __construct($page = 1, $criteria = array(), $limit = self::UNLIMITED)
    {
        $this->page     = $page ?: 1;
        $this->limit    = $limit;
        $this->elements = $criteria ? array_filter($criteria) : array();
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        if(!$this->getLimit()) {
            return null;
        }

        return $this->getLimit() * ($this->getPage() - 1);
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
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
        return isset($this->elements[$offset]) || array_key_exists($offset, $this->getElements());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->elements[$offset]) ? $this->elements[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if($value !== null && $value !== [] && $value !== '') {
            if(!isset($offset)) {
                return $this->elements[] = $value;
            }

            return $this->elements[$offset] = $value;
        }

        return $value;
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
        unset($this->elements[$offset]);

        return $removed;
    }
}
