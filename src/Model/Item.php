<?php declare(strict_types = 1);

namespace App\Model;

use DateTime;
use JsonSerializable;
use function get_object_vars;
use function is_string;

class Item implements JsonSerializable
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $description;

    /**
     * @var DateTime
     */
    private $pubDate;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Item
     */
    public function setTitle(string $title): Item
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return Item
     */
    public function setLink(string $link): Item
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     *
     * @return Item
     */
    public function setGuid(string $guid): Item
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Item
     */
    public function setDescription(string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPubDate(): DateTime
    {
        return $this->pubDate;
    }

    /**
     * @param $pubDate
     *
     * @return Item
     */
    public function setPubDate($pubDate): Item
    {
        if (is_string($pubDate)) {
            $pubDate = DateTime::createFromFormat(DateTime::RFC1123, $pubDate);
        }
        $pubDate->setTime(0, 0, 0);

        $this->pubDate = $pubDate;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
