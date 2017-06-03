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
     * Update page
     *
     * @param Page $page Page
     */
    public function update(Page $page);

    /**
     * Get page by URL
     *
     * @param string $url URL
     *
     * @return Page|null
     */
    public function getPageByURL($url);
}
