<?php

namespace Aaronadal\PersistenceBundle\Search;


/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class EmptySearchCriteria extends SearchCriteria
{

    public function __construct()
    {
        parent::__construct(1, null, null);
    }
}
