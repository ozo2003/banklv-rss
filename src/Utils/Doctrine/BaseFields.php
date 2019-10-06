<?php declare(strict_types = 1);

namespace App\Utils\Doctrine;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

trait BaseFields
{
    /**
     * @var null|int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $modifiedAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): object
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getModifiedAt(): DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime $modifiedAt
     */
    public function setModifiedAt(DateTime $modifiedAt): void
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @throws Exception
     */
    public function updatedTimestamps(): void
    {
        $this->setModifiedAt(new DateTime());

        if (null === $this->createdAt) {
            $this->setCreatedAt(new DateTime());
        }
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
