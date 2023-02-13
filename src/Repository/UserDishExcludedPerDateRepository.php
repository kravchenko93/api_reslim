<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\UserDishExcludedPerDate;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\ORM\EntityManagerInterface;

/**
 * @method UserDishExcludedPerDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDishExcludedPerDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDishExcludedPerDate[]    findAll()
 * @method UserDishExcludedPerDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDishExcludedPerDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDishExcludedPerDate::class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->_em;
    }
}