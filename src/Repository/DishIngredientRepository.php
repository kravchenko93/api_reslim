<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\DishIngredient;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DishIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishIngredient[]    findAll()
 * @method DishIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishIngredient::class);
    }

    /**
     * @param int $dishId
     *
     * @return DishIngredient|null
     */
    public function getLastDishIngredient(int $dishId): ?DishIngredient
    {
        $qb = $this->createQueryBuilder('DishIngredient');

        $qb->where($qb->expr()->eq('DishIngredient.dish', ':dishId'));
        $qb->setParameter(':dishId', $dishId);
        $qb->setMaxResults(1);
        $qb->orderBy('DishIngredient.sort', 'DESC');

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }
}