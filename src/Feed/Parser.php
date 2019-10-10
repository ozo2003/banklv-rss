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

class Parser
{
    /**
     * @param DOMDocument $document
     *
     * @return Feed
     * @throws ReflectionException
     */
    public function parse(DOMDocument $document): Feed
    {

        $element = null;
        foreach ($document->documentElement->childNodes as $node) {
            if ($node instanceof DOMElement && $node->tagName === 'channel') {
                $element = $node;
                break;
            }
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
     * @param $object
     * @param DOMElement $element
     *
     * @return mixed
     * @throws ReflectionException
     */
    private function parseElement(Element $object, DOMElement $element)
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
        if (($datetime = Date::guessDateFormat($value)) && $datetime instanceof DateTime) {
            return $datetime;
        }

        return Text::getNumeric($value);
    }
}
