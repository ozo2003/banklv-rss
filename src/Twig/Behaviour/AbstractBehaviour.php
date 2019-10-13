<?php declare(strict_types = 1);

namespace App\Twig\Behaviour;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use function is_int;
use function is_string;
use function sprintf;

abstract class AbstractBehaviour implements Behaviour
{
    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int|string $indicator
     */
    protected function guardPaginationData(int $totalPages, int $currentPage, $indicator = -1): void
    {
        if ($totalPages < 1) {
            throw new InvalidArgumentException(sprintf('Total number of pages (%d) should not be lower than 1.', $totalPages));
        }

        if ($currentPage < 1) {
            throw new InvalidArgumentException(sprintf('Current page (%d) should not be lower than 1.', $currentPage));
        }

        if ($currentPage > $totalPages) {
            throw new InvalidArgumentException(sprintf('Current page (%d) should not be higher than total number of pages (%d).', $currentPage, $totalPages));
        }

        if (!is_int($indicator) && !is_string($indicator)) {
            throw new InvalidArgumentException('Omitted pages indicator should either be a string or an int.');
        }

        if ($indicator >= 1 && $indicator <= $totalPages) {
            throw new InvalidArgumentException(\sprintf('Omitted pages indicator (%d) should not be between 1 and total number of pages (%d).', $indicator, $totalPages));
        }
    }
}
