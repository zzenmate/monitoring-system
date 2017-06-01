<?php

namespace AppBundle\Command;

use AppBundle\Entity\Page;
use AppBundle\Exception\MonitoringResourceBadResponseException;
use AppBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class MonitoringResourceCommand extends ContainerAwareCommand
{
    const POSITION_VERSION_PRINT_IN_DOM = 0;
    const COUNT_DOCUMENT_PER_PAGE = 10;
    const COUNT_DOCUMENT_PER_FAST_SCAN = 100;

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

            $listDocuments = $listCrawler->filter('.otstupVertVneshn .bg1-content a')->slice(0, 10);
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

                    $content = $this->getMainContentFromDocument(
                        $pageCrawler->filter('.otstupVertVneshn .bg1-content')->html()
                    );

                    $page = $pageRepository->getPageByURL($url);
                    if ($page instanceof Page) {
                        $page->setScannedAt(new \DateTime());

                        $hash = $this->generateHashContent($content);
                        if ($hash != $page->getHash()) {
                            $page->setContent($content.'some_kek')
                                 ->setHash($hash);
                        }
                    } else {
                        $page = (new Page())
                            ->setTitle($document->nodeValue)
                            ->setContent($content)
                            ->setHash($this->generateHashContent($content))
                            ->setUrl($url)
                            ->setScannedAt(new \DateTime());

                        $em->persist($page);
                    }
                }
            }
            $em->flush();
        }
    }

    /**
     * Get main content from document
     *
     * @param string $content Content
     *
     * @return string
     */
    protected function getMainContentFromDocument($content)
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
    protected function generateHashContent($content)
    {
        return hash('md5', $content);
    }
}
