<?php

namespace App\Services;

use App\Entity\Country;
use PhpAmqpLib\Message\AMQPMessage;
use SoapClient;

class MessageService
{
    /**
     * @var SoapClient
     */
    private $client;

    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @param SoapClient     $client
     * @param CountryService $countryService
     * @param QueueService   $queueService
     */
    public function __construct(
        SoapClient $client,
        CountryService $countryService,
        QueueService $queueService
    ) {
        $this->client = $client;
        $this->countryService = $countryService;
        $this->queueService = $queueService;
    }

    /**
     * @param AMQPMessage $msg
     */
    public function process(AMQPMessage $msg)
    {
        try {
            $countryInformation = $this->client->getCountry(['code' => $msg->body]);
            $country = $this->countryService->updateCountry($countryInformation->country);
        } catch (\SoapFault $e) {
            $country = $this->countryService->getCountryByCode($msg->body);
        }


        if ($country instanceof Country) {
            $this->queueService->validMessage($country);
        }
    }
}
