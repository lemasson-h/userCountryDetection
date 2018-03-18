<?php

namespace App\Tests\Services;

use App\Client\LocalisationClient;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Services\CountryService;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class CountryServiceTest extends TestCase
{
    /**
     * @var LocalisationClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var CountryRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $countryRepository;

    /**
     * @var CountryService
     */
    private $countryService;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createMock(LocalisationClient::class);
        $this->countryRepository = $this->createMock(CountryRepository::class);

        $this->countryService = new CountryService(
            $this->client,
            $this->countryRepository
        );
    }

    public function testGetCountryWhenGB()
    {
        $ip = '127.0.0.1';
        $code = 'GB';
        $country = $this->createMock(Country::class);

        $this->client->expects($this->once())
            ->method('getCountryInformation')
            ->with($ip)
            ->willReturn($code);
        $this->countryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'UK'])
            ->willReturn($country);
        $this->countryRepository->expects($this->never())
            ->method('create');

        static::assertSame($country, $this->countryService->getCountry($ip));
    }

    public function testGetCountryWhenItDoesNotExist()
    {
        $ip = '127.0.0.1';
        $code = 'ES';
        $country = $this->createMock(Country::class);

        $this->client->expects($this->once())
            ->method('getCountryInformation')
            ->with($ip)
            ->willReturn($code);
        $this->countryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn(null);
        $this->countryRepository->expects($this->once())
            ->method('create')
            ->with($code)
            ->willReturn($country);

        static::assertSame($country, $this->countryService->getCountry($ip));
    }

    public function testGetCountryWhenEverythingIsOk()
    {
        $ip = '127.0.0.1';
        $code = 'IE';
        $country = $this->createMock(Country::class);

        $this->client->expects($this->once())
            ->method('getCountryInformation')
            ->with($ip)
            ->willReturn($code);
        $this->countryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn($country);
        $this->countryRepository->expects($this->never())
            ->method('create');

        static::assertSame($country, $this->countryService->getCountry($ip));
    }

    public function testUpdateCountryWhenCountryNotFound()
    {
        $soapCountry = (object) [
            'code' => 'GB',
            'name' => 'United Kingdom',
            'population' => '1',
            'capital' => 'London',
            'currency' => 'GBP',
        ];

        $this->countryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'GB'])
            ->willReturn(null);
        $this->countryRepository->expects($this->never())
            ->method('save');

        static::assertNull($this->countryService->updateCountry($soapCountry));
    }

    public function testUpdateCountryWhenOk()
    {
        $soapCountry = (object) [
            'code' => 'GB',
            'name' => 'United Kingdom',
            'population' => '1',
            'capital' => 'London',
            'currency' => 'GBP',
        ];
        $country = $this->createMock(Country::class);

        $this->countryRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'GB'])
            ->willReturn($country);
        $country->method('setName')->with('United Kingdom');
        $country->method('setPopulation')->with(1);
        $country->method('setCapital')->with('London');
        $country->method('setCurrency')->with('GBP');
        $this->countryRepository->expects($this->once())
            ->method('save')
            ->with($country);

        static::assertSame($country, $this->countryService->updateCountry($soapCountry));
    }
}
