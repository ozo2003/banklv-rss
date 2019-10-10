<?php declare(strict_types = 1);

namespace App\Utils\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Vairogs\Utils\Doctrine\CreatedModified;

trait BaseFields
{
    use CreatedModified;

    /**
     * @var null|int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

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
}
