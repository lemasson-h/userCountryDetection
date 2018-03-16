<?php

namespace App\Client;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageClient
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $queueName;


    /**
     * @param string $host
     * @param int    $port
     * @param string $userName
     * @param string $password
     * @param string $queueName
     */
    public function __construct(
        string $host,
        int $port,
        string $userName,
        string $password,
        string $queueName
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->userName = $userName;
        $this->password = $password;
        $this->queueName = $queueName;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function sendMessage(string $message): void
    {
        $this->createChannel();

        $msg = new AMQPMessage(
            $message,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, '', $this->queueName);

        $this->closeChannel();
    }

    /**
     * @param callable $callable
     */
    public function readMessage(callable $callable)
    {
        $this->createChannel();

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queueName, '', false, false, false, false, $callable);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->closeChannel();
    }

    private function createChannel()
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->userName, $this->password);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true);
    }

    private function closeChannel()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
