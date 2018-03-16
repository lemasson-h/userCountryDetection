<?php

namespace App\Services;

use App\Client\MessageClient;
use App\Entity\Country;
use App\Entity\Queue;
use App\Repository\QueueRepository;
use Psr\Log\LoggerInterface;

class QueueService
{
    /**
     * @var QueueRepository
     */
    private $queueRepository;

    /**
     * @var MessageClient
     */
    private $messageClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param QueueRepository $queueRepository
     * @param MessageClient   $messageClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        QueueRepository $queueRepository,
        MessageClient $messageClient,
        LoggerInterface $logger
    ) {
        $this->queueRepository = $queueRepository;
        $this->messageClient = $messageClient;
        $this->logger = $logger;
    }

    /**
     * @param Country $country
     *
     * @return QueueService
     */
    public function sendMessageIfNecessary(Country $country)
    {
        $queued = $this->queueRepository->findOneBy(['country' => $country]);

        if ($queued instanceof Queue) {
            return $this;
        }

        try {
            $this->messageClient->sendMessage($country->getCode());
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Unable to send message to the queue, got "%s".', $e->getMessage()));
        }

        $this->queueRepository->create($country);

        return $this;
    }
}
