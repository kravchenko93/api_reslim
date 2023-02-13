<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Dish;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    /**
     * @param DateTimeImmutable $date
     * @param int $userId
     * @return Dish[]
     */
    public function findDishesForUserPerDateOrderByLogic(DateTimeImmutable $date, int $userId)
    {
        $qb = $this->createQueryBuilder('d');

        $qb->select(['d', 'ur', 'ue', 'uc'])
            ->addSelect('COALESCE(uc.date, \'0001-01-01\') AS HIDDEN uc_date')
            ->leftJoin('d.userDishRatings', 'ur', Join::WITH, 'ur.user = :userId')
            ->leftJoin('d.userDishExcludedPerDate', 'ue', Join::WITH,
                $qb->expr()->andX(
                    $qb->expr()->eq('ue.user', ':userId'),
                    $qb->expr()->eq('ue.date', ':date')
                )
            )
            ->leftJoin('d.userDishChoicePerDate', 'uc', Join::WITH, 'uc.user = :userId AND uc.date = (SELECT MAX(uc2.date) FROM \App\Entity\UserDishChoicePerDate as uc2 WHERE uc2.user = :userId and uc2.dish = d.id)')
            ->where('d.hide = false')
            //рейтинг 0 - означает, что пользователь скрыл блюдо навсегда
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('ur.rating'),
                    $qb->expr()->neq('ur.rating', 0)
                )
            )
            // не показываем исключеные на сегодня пользователем блюда
           ->andWhere(
                $qb->expr()->isNull('ue.user')
            )
            ->setParameter('userId', $userId)
            ->setParameter('date', $date->format('Y-m-d'))
            //сначала показываем блюда, которые пользователь давно не ел
            ->orderBy('uc_date', 'ASC')
            ->addOrderBy('uc.date', 'ASC')
            ->addOrderBy('ur.rating', 'DESC')
            ->addOrderBy('d.id', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }
}