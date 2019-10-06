<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Atom;
use DOMElement;
use function array_keys;
use function in_array;
use function ucfirst;

class AtomDOMElementFactory
{
    /**
     * @param DOMElement $element
     *
     * @return Atom
     */
    public static function create(DOMElement $element): Atom
    {
        $atom = new Atom();
        $fields = array_keys($atom->jsonSerialize());

        foreach ($element->attributes as $attribute) {
            if (in_array($tag = $attribute->nodeName, $fields, true)) {
                $atom->{'set' . ucfirst($tag)}($attribute->value);
            }
        }

        return $atom;
    }
}
