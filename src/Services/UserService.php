<?php

namespace App\Services;

use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * @var QueueService
     */
    private $queueService;

    public function __construct(
        UserRepository $userRepository,
        CountryService $countryService,
        QueueService $queueService
    ) {
        $this->userRepository = $userRepository;
        $this->countryService = $countryService;
        $this->queueService = $queueService;
    }

    public function getLocalisation(string $ip)
    {
        $user = $this->userRepository->findOneWithCountry($ip);

        if (null === $user) {
            $country = $this->countryService->getCountry($ip);
            $this->userRepository->create($ip, $country);
        } else {
            $country = $user->getCountry();
        }

        $this->queueService->sendMessageIfNecessary($country);

        return $country;
    }
}
