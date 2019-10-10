<?php declare(strict_types = 1);

namespace App\Model;

class Element extends Feed
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    public function setName(string $name): Element
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     *
     * @return Element
     */
    public function setValue($value): Element
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return Element
     */
    public function setAttributes(array $attributes): Element
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     *
     * @return Element
     */
    public function addAttribute(string $key, $value = null): Element
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
}
