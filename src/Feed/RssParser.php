<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Element;
use App\Model\Feed;
use DateTime;
use DOMDocument;
use DOMElement;
use ReflectionException;
use Vairogs\Utils\Date;
use Vairogs\Utils\Text;
use function count;
use const XML_ELEMENT_NODE;

class RssParser
{
    /**
     * @param DOMDocument $document
     *
     * @return Feed|null
     * @throws ReflectionException
     */
    public function parse(DOMDocument $document): ?Feed
    {
        if (null === ($element = $this->channel($document))) {
            return null;
        }

        $feed = new Feed();
        foreach ($element->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $feed->addElement($this->element($node));
            }
        }

        return $feed;
    }

    /**
     * @param DOMDocument $document
     *
     * @return DOMElement|null
     */
    private function channel(DOMDocument $document): ?DOMElement
    {
        $element = null;
        foreach ($document->documentElement->childNodes as $node) {
            if ($node instanceof DOMElement && $node->tagName === 'channel') {
                $element = $node;
                break;
            }
        }

        if (null === $element) {
            foreach ($document->childNodes as $node) {
                if ($node instanceof DOMElement && $node->tagName === 'channel') {
                    $element = $node;
                    break;
                }
            }
        }

        return $element;
    }

    /**
     * @param DOMElement $element
     *
     * @return Element
     * @throws ReflectionException
     */
    private function element(DOMElement $element): Element
    {
        $object = new Element();
        $object->setName($element->tagName);

        foreach ($element->attributes as $key => $attribute) {
            $object->addAttribute($key, $attribute->value);
        }

        return $this->parseElement($object, $element);
    }

    /**
     * @param Element $object
     * @param DOMElement $element
     *
     * @return Element
     * @throws ReflectionException
     */
    private function parseElement(Element $object, DOMElement $element): Element
    {
        if ($element->hasChildNodes()) {
            if (1 === count($element->childNodes)) {
                $object->setValue($this->fix($element->firstChild->nodeValue));
            } else {
                foreach ($element->childNodes as $node) {
                    if ($node instanceof DOMElement) {
                        $object->addElement($this->element($node));
                    }
                }
            }
        }

        return $object;
    }

    /**
     * @param $value
     *
     * @return DateTime|int|string
     * @throws ReflectionException
     */
    private function fix($value)
    {
        $value = trim($value);
        if (($datetime = Date::guessDateFormat($value)) && $datetime instanceof DateTime) {
            return $datetime;
        }

        return Text::getNumeric($value);
    }
}
