<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Image;
use DOMElement;
use function array_keys;
use function in_array;
use function is_numeric;
use function ucfirst;

class ImageDOMElementFactory
{
    /**
     * @param DOMElement $element
     *
     * @return Image
     */
    public static function create(DOMElement $element): Image
    {
        $image = new Image();
        $fields = array_keys($image->jsonSerialize());

        foreach ($element->childNodes as $child) {
            if (in_array($tag = $child->nodeName, $fields, true)) {
                if (is_numeric($data = $child->firstChild->data)) {
                    $data = (int)$data;
                }
                $image->{'set' . ucfirst($tag)}($data);
            }
        }

        return $image;
    }
}
