<?php declare(strict_types = 1);

namespace App\Model;

use DateTime;
use JsonSerializable;
use function get_object_vars;
use function is_string;

class Channel implements JsonSerializable
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $link;

    /**
     * @var DateTime
     */
    private $lastBuildDate;

    /**
     * @var string
     */
    private $generator;

    /**
     * @var Atom
     */
    private $atom;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var Item[]
     */
    private $items;

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
     * @return Channel
     */
    public function setTitle(string $title): Channel
    {
        $this->title = $title;

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
     * @return Channel
     */
    public function setDescription(string $description): Channel
    {
        $this->description = $description;

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
     * @return Channel
     */
    public function setLink(string $link): Channel
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastBuildDate(): DateTime
    {
        return $this->lastBuildDate;
    }

    /**
     * @param $lastBuildDate
     *
     * @return Channel
     */
    public function setLastBuildDate($lastBuildDate): Channel
    {
        if (is_string($lastBuildDate)) {
            $lastBuildDate = DateTime::createFromFormat(DateTime::RFC1123, $lastBuildDate);
        }
        $lastBuildDate->setTime(0, 0, 0);

        $this->lastBuildDate = $lastBuildDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getGenerator(): string
    {
        return $this->generator;
    }

    /**
     * @param string $generator
     *
     * @return Channel
     */
    public function setGenerator(string $generator): Channel
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @return Atom
     */
    public function getAtom(): Atom
    {
        return $this->atom;
    }

    /**
     * @param Atom $atom
     *
     * @return Channel
     */
    public function setAtom(Atom $atom): Channel
    {
        $this->atom = $atom;

        return $this;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     *
     * @return Channel
     */
    public function setImage(Image $image): Channel
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return Channel
     */
    public function setLanguage(string $language): Channel
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @return Channel
     */
    public function setTtl(int $ttl): Channel
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     *
     * @return Channel
     */
    public function setItems(array $items): Channel
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Channel
     */
    public function addItem(Item $item): Channel
    {
        $this->items[] = $item;

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
