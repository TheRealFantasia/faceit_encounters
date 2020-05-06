<?php

namespace App\Repository;

use App\Entity\CachedName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class CachedNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CachedName::class);
    }

    /**
     * @param CachedName $cachedName
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(CachedName $cachedName)
    {
        $em = $this->getEntityManager();

        $em->persist($cachedName);
        $em->flush();
    }
}
