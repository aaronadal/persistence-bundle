<?php

namespace Aaronadal\PersistenceBundle\DatabaseManager;


use DateInterval;
use DateTime;

/**
 * @author  Aarón Nadal <aaronadal.dev@gmail.com>
 *
 * @package Aaronadal\PersistenceBundle\DatabaseManager
 */
class DatabaseManagerUtilities
{

    /**
     * Extracts a single field form a set of entities and returns them as an array.
     *
     * NOTE: The entities must implement the method getXxx, where Xxx is the $field
     *       value passed to this method.
     *
     * @param array  $entities List of entities
     * @param string $field    Field to extract
     *
     * @return array List with the single fields of each entity
     */
    public function extractField($entities, $field = 'id')
    {
        $method = 'get' . ucfirst($field);
        $fields = [];
        foreach($entities as $key => $entity) {
            $fields[$key] = $entity->$method();
        }

        return $fields;
    }

    /**
     * Calculates a relative date.
     *
     * @param int        $yearDiff  Difference in years
     * @param int        $monthDiff Difference in months
     * @param int        $dayDiff   Difference in days
     * @param int|string $timestamp Reference timestamp
     *
     * @return DateTime The calculated date
     */
    protected function getRelativeDate($yearDiff = 0, $monthDiff = 0, $dayDiff = 0, $timestamp = 'now')
    {
        $itvstr   = "$yearDiff years, $monthDiff months, $dayDiff days";
        $interval = DateInterval::createFromDateString($itvstr);
        $date     = new DateTime($timestamp);
        $date->add($interval);

        return $date;
    }

    /**
     * Calculates a relative time.
     *
     * @param int        $hourDiff   Difference in hours
     * @param int        $minuteDiff Difference in minutes
     * @param int        $secondDiff Difference in seconds
     * @param int|string $timestamp  Reference timestamp
     *
     * @return DateTime The calculated date
     */
    protected function getRelativeTime($hourDiff = 0, $minuteDiff = 0, $secondDiff = 0, $timestamp = 'now')
    {
        $itvstr   = "$hourDiff hours, $minuteDiff minutes, $secondDiff seconds";
        $interval = DateInterval::createFromDateString($itvstr);
        $date     = new DateTime($timestamp);
        $date->add($interval);

        return $date;
    }

    /**
     * Changes the array indexes.
     *
     * @param  array $entities List of entities
     * @param string $field    Field to index. Must be a unique field, otherwise some array entries will be lost.
     *
     * @return array The indexed array
     */
    protected function indexBy($entities, $field = 'id')
    {
        $indexed = [];
        foreach($entities as $entity) {
            $key           = call_user_func([$entity, 'get' . ucfirst($field)]);
            $indexed[$key] = $entity;
        }

        return $indexed;
    }
}
