<?php

namespace AppBundle\Service;

use AppBundle\Entity\Page;
use AppBundle\Manager\MonitoringResourceManagerInterface;

/**
 * Class MonitoringResourceService
 */
class MonitoringResourceService
{
    /** @var MonitoringResourceManagerInterface $monitoringResourceManager Monitoring resource manager */
    protected $monitoringResourceManager;

    /**
     * Constructor
     *
     * @param MonitoringResourceManagerInterface $monitoringResourceManager Monitoring resource manager
     */
    public function __construct(MonitoringResourceManagerInterface $monitoringResourceManager)
    {
        $this->monitoringResourceManager = $monitoringResourceManager;
    }

    /**
     * Save new page
     *
     * @param string $title   Title
     * @param string $content Content
     * @param string $url     URL
     */
    public function savePage($title, $content, $url)
    {
        $page = (new Page())
            ->setTitle($title)
            ->setContent($content)
            ->setHash($this->generateHashContent($content))
            ->setUrl($url)
            ->setScannedAt(new \DateTime());

        $this->monitoringResourceManager->save($page);
    }

    /**
     * Get Update page
     *
     * @param Page        $page    Page
     * @param string|null $content Content
     * @param string|null $hash    Hash
     *
     * @return $page
     */
    public function getUpdatePage(Page $page, $content = null, $hash = null)
    {
        $page->setScannedAt(new \DateTime());

        if ($content != null && $hash != null) {
            $page->setContent($content)
                 ->setHash($content);
        }

        return $page;
    }

    /**
     * Remove not scanned pages
     *
     * @param int       $countScannedPages Count Scanned pages
     * @param \DateTime $startScannedAt    Start scanned at
     */
    public function removeNotScannedPages($countScannedPages, $startScannedAt)
    {
        $pages = $this->monitoringResourceManager->getNotScannedPagesByPeriod($countScannedPages, $startScannedAt);

        foreach ($pages as $page) {
            $this->monitoringResourceManager->remove($page);
        }

        $this->monitoringResourceManager->flush();
    }

    /**
     * Get main content from document
     *
     * @param string $content Content
     *
     * @return string
     */
    public function getMainContentFromDocument($content)
    {
        $startPositionH1 = strpos($content, '<h1>');
        $startPositionBlockWithLinkInFooter = strpos($content, '<div class="col-xs-4 col-sm-4');

        return substr($content, $startPositionH1, $startPositionBlockWithLinkInFooter - $startPositionH1);
    }

    /**
     * Generate hash content
     *
     * @param string $content Content
     *
     * @return string
     */
    public function generateHashContent($content)
    {
        return hash('md5', $content);
    }
}
