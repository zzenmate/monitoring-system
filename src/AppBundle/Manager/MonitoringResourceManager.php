<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Page;
use AppBundle\Repository\PageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Monolog\Logger;

/**
 * Class MonitoringResourceManager
 */
class MonitoringResourceManager implements MonitoringResourceManagerInterface
{
    /** @var EntityManager $em Entity manager */
    protected $em;

    /** @var PageRepository $pageRepository Page repository */
    protected $pageRepository;

    /** @var Logger $logger */
    protected $logger;

    /**
     * Constructor
     *
     * @param EntityManager  $em             Entity manager
     * @param PageRepository $pageRepository Page repository
     * @param Logger         $logger         Logger
     */
    public function __construct(EntityManager $em, PageRepository $pageRepository, Logger $logger)
    {
        $this->em = $em;
        $this->pageRepository = $pageRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function save(Page $page)
    {
        try {
            $this->em->persist($page);
        } catch (OptimisticLockException $e) {
            $this->logger->addError(sprintf("OptimisticLockException for ID: %s, message:\"%s\"", $page->getID(), $e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        try {
            $this->em->flush();
        } catch (OptimisticLockException $e) {
            $this->logger->addError(sprintf("OptimisticLockException, message:\"%s\"", $e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function remove(Page $page)
    {
        try {
            $this->em->remove($page);
        } catch (OptimisticLockException $e) {
            $this->logger->addError(sprintf("OptimisticLockException for ID: %s, message:\"%s\"", $page->getID(), $e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getPageByURL($url)
    {
        return $this->pageRepository->getPageByURL($url);
    }

    /**
     * @inheritdoc
     */
    public function getNotScannedPagesByPeriod($countScannedPages, $startScannedAt)
    {
        return $this->pageRepository->getNotScannedPagesByPeriod($countScannedPages, $startScannedAt);
    }
}
