<?php

namespace AppBundle\Service\Client;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    /**
     * Make GET request.
     *
     * @param string $uri URI
     * @param array  $options Options
     *
     * @return mixed|ResponseInterface
     */
    public function get($uri, $options = []);
}
