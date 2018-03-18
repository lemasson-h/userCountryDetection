<?php

namespace App\Tests\Services;

use App\Entity\Country;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\CountryService;
use App\Services\QueueService;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class UserServiceTest extends TestCase
{
    /**
     * @var UserRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $userRepository;

    /**
     * @var CountryService|PHPUnit_Framework_MockObject_MockObject
     */
    private $countryService;

    /**
     * @var QueueService|PHPUnit_Framework_MockObject_MockObject
     */
    private $queueService;

    /**
     * @var UserService
     */
    private $userService;

    protected function setUp()
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->countryService = $this->createMock(CountryService::class);
        $this->queueService = $this->createMock(QueueService::class);

        $this->userService = new UserService(
            $this->userRepository,
            $this->countryService,
            $this->queueService
        );
    }

    public function testGetLocalisationWhenUserDoesNotExist()
    {
        $ip = '127.0.0.1';
        $country = $this->createMock(Country::class);

        $this->userRepository->method('findOneWithCountry')
            ->with($ip)
            ->willReturn(null);
        $this->countryService->method('getCountry')
            ->with($ip)
            ->willReturn($country);
        $this->userRepository->expects($this->once())
            ->method('create')
            ->with($ip, $country);
        $this->queueService->expects($this->once())
            ->method('sendMessageIfNecessary')
            ->with($country);

        $this->userService->getLocalisation($ip);

    }

    public function testGetLocalisationWhenUserExist()
    {
        $ip = '127.0.0.1';
        $user = $this->createMock(User::class);
        $country = $this->createMock(Country::class);

        $this->userRepository->method('findOneWithCountry')
            ->with($ip)
            ->willReturn($user);
        $this->countryService->expects($this->never())
            ->method('getCountry');
        $this->userRepository->expects($this->never())
            ->method('create');
        $user->method('getCountry')
            ->willReturn($country);
        $this->queueService->expects($this->once())
            ->method('sendMessageIfNecessary')
            ->with($country);

        $this->userService->getLocalisation($ip);
    }
}
