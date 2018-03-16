<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Queue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Queue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Queue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Queue[]    findAll()
 * @method Queue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Queue::class);
    }

    /**
     * @param Country $country
     *
     * @return Queue
     */
    public function create(Country $country): Queue
    {
        $queue = new Queue();

        $queue->setCountry($country);
        $this->_em->persist($queue);
        $this->_em->flush();

        return $queue;
    }

    /**
     * @param Queue $queue
     *
     * @return QueueRepository
     */
    public function delete(Queue $queue): QueueRepository
    {
        $this->_em->remove($queue);
        $this->_em->flush();

        return $this;
    }
}
