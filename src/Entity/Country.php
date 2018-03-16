<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="code_idx", columns={"code"})})
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="population", type="integer")
     * @var  int
     */
    private $population;

    /**
     * @ORM\Column(name="capital", type="string")
     * @var string
     */
    private $capital;

    /**
     * @ORM\Column(name="currency", type="string")
     * @var string
     */
    private $currency;

    /**
     * @ORM\Column(name="code", type="string", nullable=false)
     * @var string
     */
    private $code;

    /**
     * @param string $name
     *
     * @return Country
     */
    public function setName(string $name): Country
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $population
     *
     * @return Country
     */
    public function setPopulation(int $population): Country
    {
        $this->population = $population;

        return $this;
    }

    /**
     * @param string $capital
     *
     * @return Country
     */
    public function setCapital(string $capital): Country
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * @param string $currency
     *
     * @return Country
     */
    public function setCurrency(string $currency): Country
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @param string $code
     *
     * @return Country
     */
    public function setCode(string $code): Country
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
