<?php

namespace App\Feed;

use App\Model\Element;
use App\Model\Feed;
use DOMDocument;
use JsonException;
use Vairogs\Utils\Json;

class JsonParser implements Parser
{
    /**
     * @param DOMDocument $document
     *
     * @return null|Feed
     */
    public function parseDocument(DOMDocument $document): ?Feed
    {
    }

    /**
     * @param string $text
     *
     * @return null|Feed
     * @throws JsonException
     */
    public function parseText(string $text): ?Feed
    {
        $feed = new Feed();

        foreach (Json::decode($text, 1) as $item) {
            $element = new Element();
            $element->setName('item');
            foreach ($item as $key => $value) {
                if (!is_array($value)) {
                    $element->addElement((new Element())->setName($key)
                        ->setValue($value));
                }
            }
            $feed->addElement($element);
        }

        return $feed;
    }
}