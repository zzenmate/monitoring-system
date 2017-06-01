<?php

namespace AppBundle\Command;

use AppBundle\Entity\Page;
use AppBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class MonitoringResourceCommand extends ContainerAwareCommand
{
    const POSITION_VERSION_PRINT_IN_DOM = 0;
    const COUNT_DOCUMENT_PER_PAGE = 10;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:tools:monitoring-resource')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var PageRepository $pageRepository */
        $pageRepository = $em->getRepository('AppBundle:Page');
        $monitoringResourceClient = $this->getContainer()->get('app.client.monitoring_resource');

        $listResponse = $monitoringResourceClient->get('');
        //         if($res->getStatusCode() == Response::HTTP_OK) {

        $listResponseContent = $listResponse->getBody()->getContents();

        $listCrawler = new Crawler($listResponseContent);
        $listDocuments = $listCrawler->filter('.otstupVertVneshn .bg1-content a');
        /** @var \DOMElement $document */
        foreach ($listDocuments as $document) {
            $url = $document->getAttribute('href');
            if (!empty($url)) {
                $page = $pageRepository->getPageByURL($url);
                if (!$page instanceof Page) {
                    $pageResponse = $monitoringResourceClient->get($url);
                    if ($pageResponse->getStatusCode() == Response::HTTP_OK) {
                        $pageResponseContent = $pageResponse->getBody()->getContents();
                        $pageCrawler = new Crawler($pageResponseContent);

                        $aad = $pageCrawler->filter('.otstupVertVneshn .bg1-content')->html();

                        $pageDomElements = $pageCrawler->filter('.otstupVertVneshn .bg1-content')->children();
                        $asd = $pageDomElements->html();
                        $pageDomElements = $this->removeLinkFromPageDomElements($pageDomElements);

                        $pageHtmlContent = $pageDomElements->html();
                    }

                    $b = 1;
                }
            }
        }

        echo 'kek';
    }

    /**
     * Remove link from page dom elements
     *
     * @param Crawler $pageDomElements Page DOM elements
     *
     * @return Crawler
     */
    protected function removeLinkFromPageDomElements(Crawler $pageDomElements)
    {
        $a = $pageDomElements->count();

        return $pageDomElements->slice(self::POSITION_VERSION_PRINT_IN_DOM, $this->getPositionVersionForAllInDom($pageDomElements));
    }

    /**
     * Get position version for all in dom
     *
     * @param Crawler $pageDomElements Page DOM elements
     *
     * @return int
     */
    protected function getPositionVersionForAllInDom(Crawler $pageDomElements)
    {
        return $pageDomElements->count() - 1;
    }
}
