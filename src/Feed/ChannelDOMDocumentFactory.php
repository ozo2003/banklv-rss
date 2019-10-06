<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Channel;
use DOMDocument;
use DOMElement;
use function array_keys;
use function dump;
use function in_array;
use function is_numeric;
use function ucfirst;

class ChannelDOMDocumentFactory
{
    /**
     * @param DOMDocument $document
     *
     * @return Channel
     */
    public static function create(DOMDocument $document): Channel
    {
        $element = null;
        foreach ($document->documentElement->childNodes as $node) {
            if ($node instanceof DOMElement && $node->tagName === 'channel') {
                $element = $node;
                break;
            }
        }

        $channel = new Channel();
        $fields = $channel->jsonSerialize();
        unset($fields['items'], $fields['image'], $fields['atom']);
        $fields = array_keys($fields);

        foreach ($element->childNodes as $node) {
            if (in_array($tag = $node->nodeName, $fields, true)) {
                if ($node->firstChild) {
                    if (is_numeric($data = $node->firstChild->data)) {
                        $data = (int)$data;
                    }
                    $channel->{'set' . ucfirst($tag)}($data);
                }
            } else {
                switch ($tag) {
                    case 'image':
                        $channel->setImage(ImageDOMElementFactory::create($node));
                        break;
                    case 'atom:link':
                        $channel->setAtom(AtomDOMElementFactory::create($node));
                        break;
                    case 'item':
                        $channel->addItem(ItemDOMElementFactory::create($node));
                        break;
                    default:
                        break;
                }
            }
        }

        return $channel;
    }
}
