<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LocalisationClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client          $client
     * @param LoggerInterface $logger
     */
    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
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
            $response = $this->client->get(sprintf('/json/%s', $ip));
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

        throw new \Exception(sprintf('Invalid response from the api for ip "%s".', $ip));
    }
}
