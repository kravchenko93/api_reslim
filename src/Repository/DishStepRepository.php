<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\DishStep;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DishStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishStep[]    findAll()
 * @method DishStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishStep::class);
    }

    /**
     * @param int $dishId
     *
     * @return DishStep|null
     */
    public function getLastDishStep(int $dishId): ?DishStep
    {
        $qb = $this->createQueryBuilder('DishStep');

        $qb->where($qb->expr()->eq('DishStep.dish', ':dishId'));
        $qb->setParameter(':dishId', $dishId);
        $qb->setMaxResults(1);
        $qb->orderBy('DishStep.sort', 'DESC');

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }
}