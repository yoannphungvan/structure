<?php
/* ---------------------------------------------------------
 * src/be/models/managers/WordManager.php
 *
 * A word manager.
 *
 * Copyright 2015 - PROJECT
 * --------------------------------------------------------- */

namespace PROJECT\Models\Managers;

/**
 * A word manager.
 * */
class WordManager extends BaseManager
{
    /**
     * Constructor.
     *
     * @param Repository $repo A repository
     * @param Object $cache A cache
     * @param ModelManagerFactory $managerFactory A manager factory
     * */
    public function __construct($repo, $cache, $managerFactory, $filters = array())
    {
        $this->table = 'word';
        $this->table_prefix = 'w';
        $this->entity = 'PROJECT\Models\Entities\Word';

        parent::__construct($repo, $cache, $managerFactory, $filters);
    }
}
