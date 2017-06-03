<?php

namespace AppBundle\Service;

use AppBundle\Entity\Page;
use Doctrine\ORM\EntityManager;

/**
 * Class MonitoringResourceService
 */
class MonitoringResourceService
{
    /** @var EntityManager $em Entity manager */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em Entity manager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

        $this->em->persist($page);
        $this->em->flush();
    }

    /**
     * Update page
     *
     * @param Page        $page    Page
     * @param string|null $content Content
     * @param string|null $hash    Hash
     */
    public function updatePage(Page $page, $content = null, $hash = null)
    {
        $page->setScannedAt(new \DateTime());

        if ($content != null && $hash != null) {
            $page->setContent($content)
                 ->setHash($content);
        }

        $this->em->flush();
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
