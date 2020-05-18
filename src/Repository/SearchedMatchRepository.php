<?php

namespace App\Repository;

use App\Entity\SearchedMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class SearchedMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchedMatch::class);
    }

    /**
     * @param SearchedMatch $searchedMatch
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(SearchedMatch $searchedMatch)
    {
        $em = $this->getEntityManager();

        $em->persist($searchedMatch);
        $em->flush();
    }
}
