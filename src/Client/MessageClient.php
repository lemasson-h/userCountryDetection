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
     * string $message
     *
     * @return void
     */
    public function sendMessage(string $message): void
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->userName, $this->password);
        $channel = $connection->channel();
        $channel->queue_declare($this->queueName, false, true);

        $msg = new AMQPMessage($message);

        $channel->basic_publish($msg, '', $this->queueName);

        $channel->close();
        $connection->close();
    }
}
