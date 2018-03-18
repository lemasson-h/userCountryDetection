<?php

namespace App\Tests\Client;

use App\Client\LocalisationClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class LocalisationClientTest extends TestCase
{
    /**
     * @var Client|PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var LocalisationClient
     */
    private $localisationClient;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->localisationClient = new LocalisationClient($this->client, $this->logger);
    }

    public function testGetCountryInformationWhenEverythingIsFine()
    {
        $ip = '127.0.0.1';
        $body = json_encode([
            'countryCode' => 'GB',
        ]);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $this->client->method('__call')
            ->with('get', ['/json/' . $ip])
            ->willReturn($response);
        $response->method('getBody')
            ->willReturn($stream);
        $stream->method('getContents')
            ->willReturn($body);

        static::assertEquals('GB', $this->localisationClient->getCountryInformation($ip));
    }

    public function testGetCountryInformationWhenGuzzleFailWithoutResponse()
    {
        $ip = '127.0.0.1';
        $e = new RequestException('', $this->createMock(RequestInterface::class));
        $this->client->method('__call')
            ->with('get', ['/json/' . $ip])
            ->willThrowException($e);
        $this->logger->expects($this->once())
            ->method('error');

        $this->expectException(RequestException::class);

        $this->localisationClient->getCountryInformation($ip);
    }

    public function testGetCountryThrowExceptionWhenInvalidBody()
    {
        $ip = '127.0.0.1';
        $body = json_encode([]);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $this->client->method('__call')
            ->with('get', ['/json/' . $ip])
            ->willReturn($response);
        $response->method('getBody')
            ->willReturn($stream);
        $stream->method('getContents')
            ->willReturn($body);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('Invalid response from the api for ip "%s".', $ip));

        $this->localisationClient->getCountryInformation($ip);
    }
}
