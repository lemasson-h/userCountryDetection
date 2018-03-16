<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LocalisationClient extends Client
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $config = [], LoggerInterface $logger)
    {
        parent::__construct($config);
    }

    /**
     * @param string $ip
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCountryInformation(string $ip)
    {
        try {
            $response = $this->get(sprintf('/json/%s', $ip));
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $this->logger->error(sprintf(
                'Unable to get localisation information from the api for ip "%s". Response ().',
                $ip,
                $response instanceof ResponseInterface ? (string) $response->getBody() : 'No response.'
            ));

            throw $e;
        }

        $body = json_decode($response->getBody()->getContents(), true);

        if (isset($body['countryCode'])) {
            return $body['countryCode'];
        }

        throw new \Exception(sprintf('Invalid response from the api for ip "%s".'));
    }
}
