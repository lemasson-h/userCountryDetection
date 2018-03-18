<?php

namespace App\Tests\Services;

use App\Entity\Country;
use App\Services\CountryService;
use App\Services\MessageService;
use App\Services\QueueService;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use SoapClient;

class MessageServiceTest extends TestCase
{
    /**
     * @var SoapClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var CountryService|PHPUnit_Framework_MockObject_MockObject
     */
    private $countryService;

    /**
     * @var QueueService|PHPUnit_Framework_MockObject_MockObject
     */
    private $queueService;

    /**
     * @var MessageService
     */
    private $messageService;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(SoapClient::class);
        $this->countryService = $this->createMock(CountryService::class);
        $this->queueService = $this->createMock(QueueService::class);

        $this->messageService = new MessageService(
            $this->client,
            $this->countryService,
            $this->queueService
        );
    }

    public function testProcessWhenCountryFoundAndNoSoapFault()
    {
        $body = 'GB';
        $countryInformation = (object) [
            'country' => (object) [
                'name' => 'UK',
                'code' => 'GB',
            ]
        ];
        $msg = $this->createMock(AMQPMessage::class);
        $country = $this->createMock(Country::class);
        $msg->body = $body;

        $this->client->method('__call')
            ->with('getCountry', [['code' => $body]])
            ->willReturn($countryInformation);
        $this->countryService->expects($this->once())
            ->method('updateCountry')
            ->with($countryInformation->country)
            ->willReturn($country);
        $this->countryService->expects($this->never())
            ->method('getCountryByCode');
        $this->queueService->expects($this->once())
            ->method('validMessage')
            ->with($country);

        $this->messageService->process($msg);
    }

    public function testProcessWhenSoapFault()
    {
        $body = 'GB';
        $msg = $this->createMock(AMQPMessage::class);
        $country = $this->createMock(Country::class);
        $msg->body = $body;

        $this->client->method('__call')
            ->with('getCountry', [['code' => $body]])
            ->willThrowException(new \SoapFault('Client', ''));
        $this->countryService->expects($this->never())
            ->method('updateCountry');
        $this->countryService->expects($this->once())
            ->method('getCountryByCode')
            ->with($body)
            ->willReturn($country);
        $this->queueService->expects($this->once())
            ->method('validMessage')
            ->with($country);

        $this->messageService->process($msg);
    }

    public function testProcessWhenCountryNotFound()
    {
        $body = 'GB';
        $countryInformation = (object) [
            'country' => (object) [
                'name' => 'UK',
                'code' => 'GB',
            ]
        ];
        $msg = $this->createMock(AMQPMessage::class);
        $country = $this->createMock(Country::class);
        $msg->body = $body;

        $this->client->method('__call')
            ->with('getCountry', [['code' => $body]])
            ->willReturn($countryInformation);
        $this->countryService->expects($this->once())
            ->method('updateCountry')
            ->with($countryInformation->country)
            ->willReturn(null);
        $this->countryService->expects($this->never())
            ->method('getCountryByCode');
        $this->queueService->expects($this->never())
            ->method('validMessage');

        $this->messageService->process($msg);
    }
}
