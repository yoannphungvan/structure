<?php

/* ---------------------------------------------------------
 * src/be/models/managers/CountryManager.php
 *
 * an country manager.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Managers

{
    use PROJECT\Models\Entities\Country;

    /**
     * an country manager.
     */
    class CountryManager extends BaseManager
    {
        /**
         * Constructor.
         *
         * @param Repository $repo A repository
         * @param Object $cache A cache
         * @param ModelManagerFactory $managerFactory A manager factory
         */
        public function __construct($repo, $cache, $managerFactory, $filters = array())
        {
            $this->table = 'country';
            $this->entity = 'PROJECT\Models\Entities\Country';

            parent::__construct($repo, $cache, $managerFactory, $filters);
        }
    }
}
