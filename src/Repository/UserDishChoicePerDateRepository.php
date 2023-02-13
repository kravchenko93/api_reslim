<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\UserDishChoicePerDate;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\ORM\EntityManagerInterface;
use \DateTimeImmutable;

/**
 * @method UserDishChoicePerDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDishChoicePerDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDishChoicePerDate[]    findAll()
 * @method UserDishChoicePerDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDishChoicePerDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDishChoicePerDate::class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->_em;
    }

    /**
     * @param int $userId
     * @param int $dishCategoryId
     * @param DateTimeImmutable $date
     *
     * @return UserDishChoicePerDate[]
     */
    public function findByUserAndDishCategoryByDate(
        int $userId,
        int $dishCategoryId,
        DateTimeImmutable $date
    ): array
    {
        $qb = $this->createQueryBuilder('UserDishChoicePerDate');
        $qb->join('UserDishChoicePerDate.dish', 'dish');
        $qb->where($qb->expr()->eq('UserDishChoicePerDate.user', ':userId'));
        $qb->andWhere($qb->expr()->eq('UserDishChoicePerDate.date', ':date'));
        $qb->andWhere($qb->expr()->eq('dish.dishCategory', ':dishCategoryId'));
        $qb->setParameter(':userId', $userId);
        $qb->setParameter(':date', $date->format('Y-m-d'));
        $qb->setParameter(':dishCategoryId', $dishCategoryId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @param int $dishId
     * @param DateTimeImmutable $date
     *
     * @return UserDishChoicePerDate[]
     */
    public function findByUserAndDishByDate(
        int $userId,
        int $dishId,
        DateTimeImmutable $date
    ): array
    {
        $qb = $this->createQueryBuilder('UserDishChoicePerDate');
        $qb->where($qb->expr()->eq('UserDishChoicePerDate.user', ':userId'));
        $qb->andWhere($qb->expr()->eq('UserDishChoicePerDate.date', ':date'));
        $qb->andWhere($qb->expr()->eq('UserDishChoicePerDate.dish', ':dishId'));
        $qb->setParameter(':userId', $userId);
        $qb->setParameter(':date', $date->format('Y-m-d'));
        $qb->setParameter(':dishId', $dishId);

        return $qb->getQuery()->getResult();
    }
}