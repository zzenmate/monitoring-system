<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Page;

/**
 * Interface PageManagerInterface
 */
interface PageManagerInterface
{
    /**
     * Save page
     *
     * @param Page $page Page
     */
    public function save(Page $page);

    /**
     * Remove page
     *
     * @param Page $page Page
     */
    public function remove(Page $page);

    /**
     * Clear entity manager
     */
    public function clear();

    /**
     * Flush page
     */
    public function flush();

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
