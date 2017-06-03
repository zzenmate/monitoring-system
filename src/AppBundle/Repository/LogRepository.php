<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Log;
use AppBundle\Entity\Page;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Log Repository
 */
class LogRepository extends LogEntryRepository
{
    /**
     * Find revisions by page
     *
     * @param Page $page Page
     *
     * @return Log[]
     */
    public function findRevisionsByPage(Page $page)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->where($qb->expr()->eq('p.objectId', ':page'))
                  ->andWhere($qb->expr()->neq('p.action', ':not_equal_action'))
                  ->setParameters([
                      ':page' => $page,
                      ':not_equal_action' => 'remove',
                  ])
                  ->orderBy('p.id', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
