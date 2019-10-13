<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use function lcfirst;
use function method_exists;
use function sprintf;
use function substr;

class Feed
{
    /**
     * @var ArrayCollection
     */
    protected $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    /**
     * @param Element $element
     *
     * @return Feed
     */
    public function addElement(Element $element): Feed
    {
        $this->elements->add($element);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems(): ArrayCollection
    {
        $collection = new ArrayCollection();

        foreach ($this->getElements() as $element) {
            /** @var Element $element */
            if ($element->getName() === 'item') {
                $collection->add($element);
            }
        }

        return $collection;
    }

    /**
     * @return ArrayCollection
     */
    public function getElements(): ArrayCollection
    {
        return $this->elements;
    }

    /**
     * @param ArrayCollection $elements
     *
     * @return Feed
     */
    public function setElements(ArrayCollection $elements): Feed
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * @param $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments = null)
    {
        if (method_exists($this, $name) && $result = $this->{$name}($arguments)) {
            return $result;
        }

        $prefix = substr($name, 0, 3);
        $key = lcfirst(substr($name, 3));
        if ('get' === $prefix && null !== ($element = $this->getElement($key))) {
            return $element->getValue();
        }

        throw new InvalidArgumentException(sprintf('Invalid function %s', $name));
    }

    /**
     * @param string $key
     *
     * @return Element|null
     */
    public function getElement(string $key): ?Element
    {
        foreach ($this->getElements() as $element) {
            /** @var Element $element */
            if ($element->getName() === $key) {
                return $element;
            }
        }

        return null;
    }
}
