<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Exchange;
use App\Entity\Rate;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use function checkdate;
use function count;
use function explode;
use function sprintf;

class RateRepository extends EntityRepository
{
    /**
     * @param DateTime $dateTime
     * @param Exchange $exchange
     * @param string|null $currency
     *
     * @return bool
     */
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

    /**
     * @return array
     */
    public function getDates(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('DATE(r.publishedAt) as publishedAt')
            ->distinct();

        return $qb->getQuery()
            ->getScalarResult();
    }

    /**
     * @param $date
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function findRateCount($date): int
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Rate::class, 'r');
        $sql = '
            SELECT 
                r.id, 
                r.currency_id, 
                MAX(r.rate) as rate, 
                r.published_at 
            FROM 
                rate r 
            WHERE 
                DATE(r.published_at) = DATE(?) 
            GROUP BY 
                r.currency_id, 
                DATE(r.published_at);
        ';
        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $this->getDateObject($date));

        return count($query->getResult());
    }

    /**
     * @param $date
     *
     * @return DateTime
     * @throws NonUniqueResultException
     */
    private function getDateObject($date): DateTime
    {
        if ('latest' === $date) {
            $qb = $this->createQueryBuilder('r');
            $qb->select('max(r.publishedAt)')
                ->distinct();
            $result = $qb->getQuery()
                ->getSingleScalarResult();
            if (null === $result) {
                $dateObject = new DateTime();
            } else {
                $dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $result);
            }
        } else {
            $dmy = explode('.', $date);
            if (3 !== count($dmy)) {
                throw new InvalidArgumentException(sprintf('Invalid date %s! Date must be in dd.mm.YYYY format', $date));
            }
            [
                $d,
                $m,
                $y,
            ] = $dmy;
            if (!checkdate((int)$m, (int)$d, (int)$y)) {
                throw new InvalidArgumentException(sprintf('Invalid date %s', $date));
            }
            $dateObject = DateTime::createFromFormat('d.m.Y', $date);
        }

        if (false === $dateObject) {
            throw new InvalidArgumentException(sprintf('Invalid date %s', $date));
        }

        $dateObject->setTime(0, 0, 0);

        return $dateObject;
    }

    /**
     * @param $date
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws NonUniqueResultException
     */
    public function findRates($date, int $limit, int $offset = 0): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Rate::class, 'r');
        $sql = '
            SELECT 
                r.id, 
                r.currency_id, 
                MAX(r.rate) as rate, 
                r.published_at 
            FROM 
                rate r 
            LEFT JOIN
                currency c ON r.currency_id = c.id
            WHERE 
                DATE(r.published_at) = DATE(?) 
            GROUP BY 
                r.currency_id, 
                DATE(r.published_at) 
            ORDER BY
                c.code ASC
            LIMIT ?
        ';

        if (0 !== $offset) {
            $sql .= ' OFFSET ?';
        }

        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $this->getDateObject($date));
        $query->setParameter(2, $limit);

        if (0 !== $offset) {
            $query->setParameter(3, $offset);
        }

        return $query->getResult();
    }
}
