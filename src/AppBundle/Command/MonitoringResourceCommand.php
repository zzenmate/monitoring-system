<?php

namespace AppBundle\Command;

use AppBundle\Entity\Page;
use AppBundle\Exception\MonitoringResourceBadResponseException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Console\Exception\RuntimeException;

class MonitoringResourceCommand extends ContainerAwareCommand
{
    const COUNT_DOCUMENT_PER_PAGE = 10;
    const COUNT_DOCUMENT_PER_FAST_SCAN = 100;
    const COUNT_DOCUMENT_ELEMENT_IN_EMPTY_PAGE = 2;
    const MAX_COUNT_SCAN_DOCUMENT = 100000;
    const MODE_FAST = 'fast';
    const MODE_FULL = 'full';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:tools:monitoring-resource')
            ->setDescription('Command for monitoring resource')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'mode: fast or full', self::MODE_FAST);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $input->getOption('mode');
        if ($mode != self::MODE_FAST && $mode != self::MODE_FULL) {
            throw new RuntimeException(sprintf('Incorrect mode'));
        }

        $batchSize = 200;
        $countIteration = 0;
        $startScannedAt = (new \DateTime());

        $monitoringResourceClient = $this->getContainer()->get('app.client.monitoring_resource');
        $monitoringResourceManager = $this->getContainer()->get('app.monitoring_resource.manager');
        $monitoringResourceService = $this->getContainer()->get('app.monitoring_resource');

        if ($mode == self::MODE_FAST) {
            $countScanPage = self::COUNT_DOCUMENT_PER_FAST_SCAN / self::COUNT_DOCUMENT_PER_PAGE;
        } else {
            $countScanPage = self::MAX_COUNT_SCAN_DOCUMENT;
        }

        // if page does not have list with documents, "FOR" will be stop work
        for ($i = 0; $i < $countScanPage; $i++) {
            try {
                $listResponse = $monitoringResourceClient->get('', [
                    'query' => [
                        'start' => 8850 + self::COUNT_DOCUMENT_PER_PAGE * $i,
                    ],
                ]);
            } catch (MonitoringResourceBadResponseException $e) {
                $output->writeln($e->getMessage());

                return;
            }

            $listResponseContent = $listResponse->getBody()->getContents();

            $listCrawler = new Crawler($listResponseContent);

            $listDocumentLinks = $listCrawler->filter('.otstupVertVneshn .bg1-content a');

            $countDocuments = $listDocumentLinks->count()
                              > 10 ? self::COUNT_DOCUMENT_PER_PAGE : $listDocumentLinks->count();

            if ($countDocuments <= self::COUNT_DOCUMENT_ELEMENT_IN_EMPTY_PAGE) {
                break;
            }

            $listDocuments = $listDocumentLinks->slice(0, $countDocuments);
            /** @var \DOMElement $document */
            foreach ($listDocuments as $document) {
                $countIteration++;

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
                            $monitoringResourceService->getUpdatePage($page);
                        } else {
                            $monitoringResourceService->getUpdatePage($page, $content, $hash);
                        }
                    } else {
                        $monitoringResourceService->savePage($title, $content, $url);
                    }
                }

                if (($i % $batchSize) == 0) {
                    $monitoringResourceManager->flush();
                    $monitoringResourceManager->clear();
                }
            }
        }

        $monitoringResourceManager->flush();

        $monitoringResourceService->removeNotScannedPages($countIteration, $startScannedAt);
    }
}
