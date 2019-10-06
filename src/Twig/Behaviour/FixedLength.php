<?php declare(strict_types = 1);

namespace App\Twig\Behaviour;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use function array_merge;
use function ceil;
use function floor;
use function range;
use function sprintf;

class FixedLength extends AbstractBehaviour
{
    public const MIN_VISIBLE = 3;

    /**
     * @var int
     */
    private $maximumVisible;

    /**
     * @param int $maximumVisible
     */
    public function __construct(int $maximumVisible)
    {
        $this->setMaximumVisible($maximumVisible);
    }

    /**
     * @param int $maximumVisible
     */
    private function setMaximumVisible(int $maximumVisible): void
    {
        $this->guardMaximumVisibleMinimumValue($maximumVisible);
        $this->maximumVisible = $maximumVisible;
    }

    /**
     * @param int $maximumVisible
     */
    private function guardMaximumVisibleMinimumValue(int $maximumVisible): void
    {
        if ($maximumVisible < self::MIN_VISIBLE) {
            throw new InvalidArgumentException(sprintf('Maximum of number of visible pages (%d) should be at least %d.', $maximumVisible, self::MIN_VISIBLE));
        }
    }

    /**
     * @param int $maximumVisible
     *
     * @return FixedLength
     */
    public function withMaximumVisible(int $maximumVisible): FixedLength
    {
        $clone = clone $this;
        $clone->setMaximumVisible($maximumVisible);

        return $clone;
    }

    /**
     * @return int
     */
    public function getMaximumVisible(): int
    {
        return $this->maximumVisible;
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int $omittedPagesIndicator
     *
     * @return array
     */
    public function getPaginationData(int $totalPages, int $currentPage, int $omittedPagesIndicator = -1): array
    {
        $this->guardPaginationData($totalPages, $currentPage, $omittedPagesIndicator);
        if ($totalPages <= $this->maximumVisible) {
            return $this->getPaginationDataWithNoOmittedChunks($totalPages);
        }
        if ($this->hasSingleOmittedChunk($totalPages, $currentPage)) {
            return $this->getPaginationDataWithSingleOmittedChunk($totalPages, $currentPage, $omittedPagesIndicator);
        }

        return $this->getPaginationDataWithTwoOmittedChunks($totalPages, $currentPage, $omittedPagesIndicator);
    }

    /**
     * @param int $totalPages
     *
     * @return array
     */
    private function getPaginationDataWithNoOmittedChunks(int $totalPages): array
    {
        return range(1, $totalPages);
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     *
     * @return bool
     */
    public function hasSingleOmittedChunk(int $totalPages, int $currentPage): bool
    {
        return $this->hasSingleOmittedChunkNearLastPage($currentPage) || $this->hasSingleOmittedChunkNearStartPage($totalPages, $currentPage);
    }

    /**
     * @param int $currentPage
     *
     * @return bool
     */
    private function hasSingleOmittedChunkNearLastPage(int $currentPage): bool
    {
        return $currentPage <= $this->getSingleOmissionBreakpoint();
    }

    /**
     * @return int
     */
    private function getSingleOmissionBreakpoint(): int
    {
        return (int)floor($this->maximumVisible / 2) + 1;
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     *
     * @return bool
     */
    private function hasSingleOmittedChunkNearStartPage(int $totalPages, int $currentPage): bool
    {
        return $currentPage >= $totalPages - $this->getSingleOmissionBreakpoint() + 1;
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int $omittedPagesIndicator
     *
     * @return array
     */
    private function getPaginationDataWithSingleOmittedChunk(int $totalPages, int $currentPage, int $omittedPagesIndicator): array
    {
        if ($this->hasSingleOmittedChunkNearLastPage($currentPage)) {
            $rest = $this->maximumVisible - $currentPage;
            $omitPagesFrom = ((int)ceil($rest / 2)) + $currentPage;
            $omitPagesTo = $totalPages - ($this->maximumVisible - $omitPagesFrom);
        } else {
            $rest = $this->maximumVisible - ($totalPages - $currentPage);
            $omitPagesFrom = (int)ceil($rest / 2);
            $omitPagesTo = ($currentPage - ($rest - $omitPagesFrom));
        }
        $pagesLeft = range(1, $omitPagesFrom - 1);
        $pagesRight = range($omitPagesTo + 1, $totalPages);

        return array_merge($pagesLeft, [$omittedPagesIndicator], $pagesRight);
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int $omittedPagesIndicator
     *
     * @return array
     */
    private function getPaginationDataWithTwoOmittedChunks(int $totalPages, int $currentPage, int $omittedPagesIndicator): array
    {
        $visibleExceptForCurrent = $this->maximumVisible - 1;
        if ($currentPage <= ceil($totalPages / 2)) {
            $visibleLeft = ceil($visibleExceptForCurrent / 2);
            $visibleRight = floor($visibleExceptForCurrent / 2);
        } else {
            $visibleLeft = floor($visibleExceptForCurrent / 2);
            $visibleRight = ceil($visibleExceptForCurrent / 2);
        }
        $omitPagesLeftFrom = floor($visibleLeft / 2) + 1;
        $omitPagesLeftTo = $currentPage - ($visibleLeft - $omitPagesLeftFrom) - 1;
        $omitPagesRightFrom = ceil($visibleRight / 2) + $currentPage;
        $omitPagesRightTo = $totalPages - ($visibleRight - ($omitPagesRightFrom - $currentPage));
        $pagesLeft = range(1, $omitPagesLeftFrom - 1);
        $pagesCenter = range($omitPagesLeftTo + 1, $omitPagesRightFrom - 1);
        $pagesRight = range($omitPagesRightTo + 1, $totalPages);

        return array_merge($pagesLeft, [$omittedPagesIndicator], $pagesCenter, [$omittedPagesIndicator], $pagesRight);
    }
}
