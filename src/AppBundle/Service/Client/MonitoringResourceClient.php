<?php

namespace AppBundle\Service\Client;

use AppBundle\Exception\MonitoringResourceBadResponseException;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MonitoringResourceClient
 */
class MonitoringResourceClient implements ClientInterface
{
    /** @var GuzzleClient */
    protected $guzzleClient;

    /**
     * Constructor
     *
     * @param GuzzleClient $guzzleClient Guzzle Client
     */
    public function __construct(GuzzleClient $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri, $options = [])
    {
        return $this->doRequest(Request::METHOD_GET, $uri, $options);
    }

    /**
     * @param string     $method  Method
     * @param string     $uri     URI
     * @param array|null $options Options
     *
     * @throws \Exception
     *
     * @return null|ResponseInterface
     */
    private function doRequest($method, $uri, $options)
    {
        try {
            $response = $this->guzzleClient->request($method, $uri, $options);
        } catch (MonitoringResourceBadResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}
