<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class Currency
{
    /**
     * @var string
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    protected $code;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): object
    {
        $this->code = $code;

        return $this;
    }
}
