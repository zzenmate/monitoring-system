<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Page;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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

    /**
     * Get not scanned pages by period
     *
     * @param int       $countScannedPages Count Scanned pages
     * @param \DateTime $startScannedAt    start scanned at
     *
     * @return Page[]
     */
    public function getNotScannedPagesByPeriod($countScannedPages, $startScannedAt)
    {
        $qb1 = $this->createQueryBuilder('p1');
        $qb2 = $this->createQueryBuilder('p2');

        $pagesIDs = $qb2
            ->select('p2.id')
            ->setFirstResult(0)
            ->setMaxResults($countScannedPages)
            ->getQuery()
            ->getResult();

        return $qb1->where($qb1->expr()->lt('p1.scannedAt', ':scanned_at'))
                   ->andWhere(
                       $qb1->expr()->in('p1.id', ':page_ids')
                   )
                   ->setParameters([
                       'page_ids' => $pagesIDs,
                       'scanned_at' => $startScannedAt->format('Y-m-d H:i:s'),
                   ])
                   ->getQuery()
                   ->getResult();
    }
}
