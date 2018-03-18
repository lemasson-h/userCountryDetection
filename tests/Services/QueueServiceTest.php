<?php

namespace App\Tests\Services;

use App\Client\MessageClient;
use App\Entity\Country;
use App\Entity\Queue;
use App\Repository\QueueRepository;
use App\Services\QueueService;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

class QueueServiceTest extends TestCase
{
    /**
     * @var QueueRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $queueRepository;

    /**
     * @var MessageClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $messageClient;

    /**
     * @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var QueueService
     */
    private $queueService;

    protected function setUp()
    {
        parent::setUp();

        $this->queueRepository = $this->createMock(QueueRepository::class);
        $this->messageClient = $this->createMock(MessageClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->queueService = new QueueService(
            $this->queueRepository,
            $this->messageClient,
            $this->logger
        );
    }

    public function testSendMessageDoesNotResendWhenThereIsAlreadyAMessage()
    {
        $country = $this->createMock(Country::class);
        $queued = $this->createMock(Queue::class);

        $this->queueRepository->method('findOneBy')
            ->with(['country' => $country])
            ->willReturn($queued);
        $this->messageClient->expects($this->never())
            ->method('sendMessage');
        $this->queueRepository->expects($this->never())
            ->method('create');

        static::assertSame(
            $this->queueService,
            $this->queueService->sendMessageIfNecessary($country)
        );
    }

    public function testSendMessageAddAMessageToRabbitMqAndAQueueEntity()
    {
        $code = 'IE';
        $country = $this->createMock(Country::class);

        $this->queueRepository->method('findOneBy')
            ->with(['country' => $country])
            ->willReturn(null);
        $country->method('getCode')
            ->willReturn($code);
        $this->messageClient->expects($this->once())
            ->method('sendMessage')
            ->with($code);
        $this->queueRepository->expects($this->once())
            ->method('create')
            ->with($country);

        static::assertSame(
            $this->queueService,
            $this->queueService->sendMessageIfNecessary($country)
        );
    }

    public function testSendMessageLogErrorAndDoesNotCreateQueueEntityWhenExceptionWhileTryingToSendMessageToRabbitMQ()
    {
        $code = 'IE';
        $country = $this->createMock(Country::class);

        $this->queueRepository->method('findOneBy')
            ->with(['country' => $country])
            ->willReturn(null);
        $country->method('getCode')
            ->willReturn($code);
        $this->messageClient->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(new \Exception('error'));
        $this->logger->expects($this->once())
            ->method('error');
        $this->queueRepository->expects($this->never())
            ->method('create');

        static::assertSame(
            $this->queueService,
            $this->queueService->sendMessageIfNecessary($country)
        );
    }

    public function testValidMessageWhenNoQueueEntityDoesNothing()
    {
        $country = $this->createMock(Country::class);

        $this->queueRepository->method('findOneBy')
            ->with(['country' => $country])
            ->willReturn(null);
        $this->queueRepository->expects($this->never())
            ->method('delete');

        static::assertEquals($this->queueService, $this->queueService->validMessage($country));
    }

    public function testValidMessageDeleteQueueEntity()
    {
        $country = $this->createMock(Country::class);
        $queue = $this->createMock(Queue::class);

        $this->queueRepository->method('findOneBy')
            ->with(['country' => $country])
            ->willReturn($queue);
        $this->queueRepository->expects($this->once())
            ->method('delete')
            ->with($queue);

        static::assertEquals($this->queueService, $this->queueService->validMessage($country));
    }
}
