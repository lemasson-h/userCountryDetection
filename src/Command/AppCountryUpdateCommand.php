<?php

namespace App\Command;

use App\Client\MessageClient;
use App\Entity\Country;
use App\Services\CountryService;
use App\Services\MessageService;
use App\Services\QueueService;
use GuzzleHttp\Client;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SoapClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppCountryUpdateCommand extends Command
{
    protected static $defaultName = 'app:country-update';

    /**
     * @var MessageClient
     */
    private $messageClient;

    /**
     * @var MessageService
     */
    private $messageService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param MessageClient   $messageClient
     * @param MessageService  $messageService
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessageClient $messageClient,
        MessageService $messageService,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->messageClient = $messageClient;
        $this->messageService = $messageService;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Read message to update countries')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->messageClient->readMessage([$this, 'processMessage']);


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }

    /**
     * @param AMQPMessage $msg
     */
    public function processMessage(AMQPMessage $msg)
    {
        try {
            $this->messageService->process($msg);

            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Unable to process the message "%s", got error "%s"',
                $msg->body,
                $e->getMessage() . ' - ' . $e->getTraceAsString()
            ));
        }
    }
}
