<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Page;

/**
 * Interface MonitoringResourceManagerInterface
 */
interface MonitoringResourceManagerInterface
{
    /**
     * Save page
     *
     * @param Page $page Page
     */
    public function save(Page $page);

    /**
     * Flush page
     */
    public function flush();

    /**
     * Remove page
     *
     * @param Page $page Page
     */
    public function remove(Page $page);

    /**
     * Get page by URL
     *
     * @param string $url URL
     *
     * @return Page|null
     */
    public function getPageByURL($url);

    /**
     * Get not scanned pages by period
     *
     * @param $countScannedPages
     * @param $startScannedAt
     *
     * @return Page[]
     */
    public function getNotScannedPagesByPeriod($countScannedPages, $startScannedAt);
}
