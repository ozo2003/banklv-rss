<?php declare(strict_types = 1);

namespace App\Model;

use JsonSerializable;
use function get_object_vars;

class Atom implements JsonSerializable
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $href;

    /**
     * @return string
     */
    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * @param string $rel
     *
     * @return Atom
     */
    public function setRel(string $rel): Atom
    {
        $this->rel = $rel;

        return $this;
    }

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
     * @return Atom
     */
    public function setType(string $type): Atom
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     *
     * @return Atom
     */
    public function setHref(string $href): Atom
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
