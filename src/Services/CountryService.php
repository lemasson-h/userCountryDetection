<?php

namespace App\Services;

use App\Client\LocalisationClient;
use App\Entity\Country;
use App\Repository\CountryRepository;

class CountryService
{
    /**
     * @var LocalisationClient
     */
    private $client;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * @param LocalisationClient $client
     * @param CountryRepository  $countryRepository
     */
    public function __construct(
        LocalisationClient $client,
        CountryRepository $countryRepository
    ) {
        $this->client = $client;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param string $ip
     *
     * @return Country
     *
     * @throws \Exception
     */
    public function getCountry(string $ip): Country
    {
        $countryCode = $this->client->getCountryInformation($ip);

        //United Kingdom country code should be GB, but soap wants UK (Which is normally for Ukraine).
        if ($countryCode === 'GB') {
            $countryCode = 'UK';
        }

        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        if (null === $country) {
            $country = $this->countryRepository->create($countryCode);
        }

        return $country;
    }

    /**
     * @param string $code
     *
     * @return Country|null
     */
    public function getCountryByCode(string $code)
    {
        return $this->countryRepository->findOneBy(['code' => $code]);
    }

    /**
     * @param \stdClass $soapCountry
     *
     * @return null|Country
     */
    public function updateCountry(\stdClass $soapCountry)
    {
        $country = $this->countryRepository->findOneBy(['code' => $soapCountry->code]);

        if (null === $country) {
            return null;
        }

        $country->setName($soapCountry->name);
        $country->setPopulation($soapCountry->population);
        $country->setCapital($soapCountry->capital);
        $country->setCurrency($soapCountry->currency);

        $this->countryRepository->save($country);

        return $country;
    }
}
