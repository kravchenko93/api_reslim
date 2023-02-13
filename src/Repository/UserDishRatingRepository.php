<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\UserDishRating;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\ORM\EntityManagerInterface;

/**
 * @method UserDishRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDishRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDishRating[]    findAll()
 * @method UserDishRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDishRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDishRating::class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->_em;
    }
}