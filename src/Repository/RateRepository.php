<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Exchange;
use DateTime;
use Doctrine\ORM\EntityRepository;

class RateRepository extends EntityRepository
{
    public function isDateAlreadySubmitted(DateTime $dateTime, Exchange $exchange, ?string $currency = null): bool
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')
            ->leftJoin('r.currency', 'c')
            ->distinct()
            ->where('DATE(r.publishedAt) = DATE(:datetime)')
            ->andWhere('r.exchange = :exchange')
            ->setParameter(':datetime', $dateTime)
            ->setParameter(':exchange', $exchange)
            ->groupBy('r.exchange');

        if (null !== $currency) {
            $qb->andWhere('c.code = :currency')
                ->setParameter(':currency', $currency);
        }

        return count($qb->getQuery()
                ->getResult()) === 0;
    }
}
