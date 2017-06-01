<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Page;
use Doctrine\ORM\EntityRepository;

/**
 * Page Repository
 */
class PageRepository extends EntityRepository
{
    /**
     * Get page by URL
     *
     * @param string $url URL
     *
     * @return Page|null
     */
    public function getPageByURL($url)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->where($qb->expr()->eq('p.url', ':url'))
                  ->setParameter('url', $url)
                  ->getQuery()
                  ->getOneOrNullResult();
    }
}
