<?php declare(strict_types = 1);

namespace App\Twig\Behaviour;

interface Behaviour
{
    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int $omittedPagesIndicator
     *
     * @return array
     */
    public function getPaginationData(int $totalPages, int $currentPage, int $omittedPagesIndicator = -1): array;
}
