<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is just to keep a trace of all current message in the queue for the country, to avoid to send duplicate messages
 *
 * @ORM\Entity(repositoryClass="App\Repository\QueueRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="country_id_unique_idx", columns={"country_id"})})
 */
class Queue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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

    public function setCountry(Country $country): Queue
    {
        $this->country = $country;

        return $this;
    }
}
