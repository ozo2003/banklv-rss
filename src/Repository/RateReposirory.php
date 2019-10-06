<?php declare(strict_types = 1);

namespace App\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use function checkdate;
use function dump;
use function explode;
use function sprintf;

class RateReposirory extends EntityRepository
{
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
        $qb = $this->createQueryBuilder('r');
        $qb->select('r')->where('r.publishedAt = :dateObject')->setParameter(':dateObject', $this->getDateObject($date))->setMaxResults($limit);

        if (0 !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
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
            $qb->select('max(r.publishedAt)')->distinct();
            $result = $qb->getQuery()->getSingleScalarResult();
            if (null === $result) {
                $dateObject = new DateTime();
            } else {
                $dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $result);
            }
        } else {
            [$d, $m, $y] = explode('.', $date);
            if (!checkdate((int)$d, (int)$m, (int)$y)) {
                throw new InvalidArgumentException(sprintf('Invalid date %s! Date must be in dd.mm.YYYY format', $date));
            }
            $dateObject = DateTime::createFromFormat('d.m.Y', $date);
        }

        $dateObject->setTime(0, 0, 0);

        return $dateObject;
    }

    /**
     * @param $date
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function findRateCount($date): int
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('count(1)')->where('r.publishedAt = :dateObject')->setParameter(':dateObject', $this->getDateObject($date));

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function getDates(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r.publishedAt')->distinct();

        return $qb->getQuery()->getScalarResult();
    }
}
