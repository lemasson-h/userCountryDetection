<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $ip
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneWithCountry(string $ip)
    {
        return $this->createQueryBuilder('u')
            ->join('u.country', 'c')
            ->addSelect('c')
            ->where('u.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $ip
     * @param Country $country
     */
    public function create(string $ip, Country $country): void
    {
        $user = new User();

        $user->setIp($ip);
        $user->setCountry($country);
        $this->_em->persist($user);

        try {
            $this->_em->flush();
        } catch (UniqueConstraintViolationException $e) {
            //Avoid to crash if the same user send two requests in a really short time
        }
    }
}
