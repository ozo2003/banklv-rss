<?php declare(strict_types = 1);

namespace App\Twig\Behaviour;

interface Behaviour
{
    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int $indicator
     *
     * @return array
     */
    public function getPaginationData(int $totalPages, int $currentPage, int $indicator = -1): array;
}
