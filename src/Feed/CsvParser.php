<?php

namespace App\Feed;

use App\Model\Feed;
use DOMDocument;

class CsvParser implements Parser
{
    /**
     * @param DOMDocument $document
     *
     * @return null|Feed
     */
    public function parseDocument(DOMDocument $document): ?Feed {

    }

    /**
     * @param string $text
     *
     * @return null|Feed
     */
    public function parseText(string $text): ?Feed {

    }
}