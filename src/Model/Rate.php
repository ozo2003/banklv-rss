<?php declare(strict_types = 1);

namespace App\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class Rate
{
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $publishedAt;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     */
    protected $rate;

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     *
     * @return Rate
     */
    public function setRate(float $rate): Rate
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(DateTime $publishedAt): object
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
