<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="ip_idx", columns={"ip"})})
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="ip", type="string", nullable=false)
     * @var string
     */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): User
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}
