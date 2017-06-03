<?php

namespace AppBundle\Command;

use AppBundle\Entity\Page;
use AppBundle\Exception\MonitoringResourceBadResponseException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class MonitoringResourceCommand extends ContainerAwareCommand
{
    const POSITION_VERSION_PRINT_IN_DOM = 0;
    const COUNT_DOCUMENT_PER_PAGE = 10;
    const COUNT_DOCUMENT_PER_FAST_SCAN = 100;
    const COUNT_DOCUMENT_ELEMENT_IN_EMPTY_PAGE = 2;

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
        $monitoringResourceClient = $this->getContainer()->get('app.client.monitoring_resource');
        $monitoringResourceManager = $this->getContainer()->get('app.monitoring_resource.manager');
        $monitoringResourceService = $this->getContainer()->get('app.monitoring_resource');

        for ($i = 0; $i < self::COUNT_DOCUMENT_PER_FAST_SCAN / self::COUNT_DOCUMENT_PER_PAGE; $i++) {
            try {
                $listResponse = $monitoringResourceClient->get('', [
                    'query' => [
                        'start' => self::COUNT_DOCUMENT_PER_PAGE * $i,
                    ],
                ]);
            } catch (MonitoringResourceBadResponseException $e) {
                $output->writeln($e->getMessage());

                return;
            }

            $listResponseContent = $listResponse->getBody()->getContents();

            $listCrawler = new Crawler($listResponseContent);

            $countDocuments = $listCrawler->count() >= 10 ? 10 : $listCrawler->count();
            // Check if page empty on documents
            if ($countDocuments <= self::COUNT_DOCUMENT_ELEMENT_IN_EMPTY_PAGE) {
                return;
            }
            $listDocuments = $listCrawler->filter('.otstupVertVneshn .bg1-content a')->slice(0, $countDocuments);
            /** @var \DOMElement $document */
            foreach ($listDocuments as $document) {
                $url = $document->getAttribute('href');
                echo $url.PHP_EOL;
                if (!empty($url) && strpos($url, '.html') !== false) { // check if link to content page
                    try {
                        $pageResponse = $monitoringResourceClient->get($url);
                    } catch (MonitoringResourceBadResponseException $e) {
                        $output->writeln($e->getMessage());

                        return;
                    }

                    $pageResponseContent = $pageResponse->getBody()->getContents();
                    $pageCrawler = new Crawler($pageResponseContent);

                    $content = $monitoringResourceService->getMainContentFromDocument(
                        $pageCrawler->filter('.otstupVertVneshn .bg1-content')->html()
                    );

                    $page = $monitoringResourceManager->getPageByURL($url);
                    $title = $document->nodeValue;
                    if ($page instanceof Page) {
                        $hash = $monitoringResourceService->generateHashContent($content);
                        if ($hash == $page->getHash()) {
                            $monitoringResourceService->updatePage($page);
                        } else {
                            $monitoringResourceService->updatePage($page, $content, $hash);
                        }
                    } else {
                        $monitoringResourceService->savePage($title, $content, $url);
                    }
                }
            }
        }
    }
}
