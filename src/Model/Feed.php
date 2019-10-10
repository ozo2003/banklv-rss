<?php declare(strict_types = 1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @param string $key
     *
     * @return Element|null
     */
    public function getElement(string $key): ?Element
    {
        foreach ($this->getElements() as $element) {
            /** @var Element $element */
            if($element->getName() === $key){
                return $element;
            }
        }

        return null;
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
     * @return Item
     */
    public function setElements(ArrayCollection $elements): Item
    {
        $this->elements = $elements;

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
}
