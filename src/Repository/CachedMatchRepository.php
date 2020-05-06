<?php

namespace App\Repository;

use App\Entity\CachedMatch;
use App\Entity\CachedName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class CachedMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CachedMatch::class);
    }

    /**
     * @param CachedMatch $cachedMatch
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(CachedMatch $cachedMatch)
    {
        $em = $this->getEntityManager();

        $em->persist($cachedMatch);
        $em->flush();
    }
}
