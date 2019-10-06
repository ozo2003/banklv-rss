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
     * @param int $omittedPagesIndicator
     */
    protected function guardPaginationData(int $totalPages, int $currentPage, int $omittedPagesIndicator = -1): void
    {
        $this->guardTotalPagesMinimumValue($totalPages);
        $this->guardCurrentPageMinimumValue($currentPage);
        $this->guardCurrentPageExistsInTotalPages($totalPages, $currentPage);
        $this->guardOmittedPagesIndicatorType($omittedPagesIndicator);
        $this->guardOmittedPagesIndicatorIntValue($totalPages, $omittedPagesIndicator);
    }

    /**
     * @param int $totalPages
     */
    private function guardTotalPagesMinimumValue(int $totalPages): void
    {
        if ($totalPages < 1) {
            throw new InvalidArgumentException(sprintf('Total number of pages (%d) should not be lower than 1.', $totalPages));
        }
    }

    /**
     * @param int $currentPage
     */
    private function guardCurrentPageMinimumValue(int $currentPage): void
    {
        if ($currentPage < 1) {
            throw new InvalidArgumentException(sprintf('Current page (%d) should not be lower than 1.', $currentPage));
        }
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     */
    private function guardCurrentPageExistsInTotalPages(int $totalPages, int $currentPage): void
    {
        if ($currentPage > $totalPages) {
            throw new InvalidArgumentException(sprintf('Current page (%d) should not be higher than total number of pages (%d).', $currentPage, $totalPages));
        }
    }

    /**
     * @param $indicator
     */
    private function guardOmittedPagesIndicatorType($indicator): void
    {
        if (!is_int($indicator) && !is_string($indicator)) {
            throw new InvalidArgumentException('Omitted pages indicator should either be a string or an int.');
        }
    }

    /**
     * @param int $totalPages
     * @param int $indicator
     */
    private function guardOmittedPagesIndicatorIntValue(int $totalPages, int $indicator): void
    {
        if ($indicator >= 1 && $indicator <= $totalPages) {
            throw new InvalidArgumentException(\sprintf('Omitted pages indicator (%d) should not be between 1 and total number of pages (%d).', $indicator, $totalPages));
        }
    }
}
