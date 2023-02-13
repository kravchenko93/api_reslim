<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\YookassaPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use \Doctrine\ORM\EntityManagerInterface;

/**
 * @method YookassaPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method YookassaPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method YookassaPayment[]    findAll()
 * @method YookassaPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YookassaPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YookassaPayment::class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface {
        return $this->_em;
    }
}