<?php

namespace App\Repository;

use App\Entity\Narqd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @extends ServiceEntityRepository<Narqd>
 *
 * @method Narqd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Narqd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Narqd[]    findAll()
 * @method Narqd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NarqdRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $registry)
    {
        parent::__construct($registry,$registry->getClassMetadata(Narqd::class));
    }
}
