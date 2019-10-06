<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Item;
use DOMElement;
use function array_keys;
use function in_array;
use function is_numeric;
use function ucfirst;

class ItemDOMElementFactory
{
    /**
     * @param DOMElement $element
     *
     * @return Item
     */
    public static function create(DOMElement $element): Item
    {
        $item = new Item();
        $fields = array_keys($item->jsonSerialize());

        foreach ($element->childNodes as $child) {
            if (in_array($tag = $child->nodeName, $fields, true)) {
                if (is_numeric($data = $child->firstChild->data)) {
                    $data = (int)$data;
                }
                $item->{'set' . ucfirst($tag)}($data);
            }
        }

        return $item;
    }
}
