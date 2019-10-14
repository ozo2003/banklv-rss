<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class Exchange
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Exchange
     */
    public function setType(string $type): Exchange
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Exchange
     */
    public function setTitle(string $title): Exchange
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Exchange
     */
    public function setUrl(string $url): Exchange
    {
        $this->url = $url;

        return $this;
    }
}
