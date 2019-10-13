<?php declare(strict_types = 1);

namespace App\Feed;

use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Rate;
use App\Model\Element;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Vairogs\Utils\Text;
use function array_merge;
use function count;
use function explode;
use function preg_match_all;
use function str_replace;
use function trim;

class RateFacade
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array Currency[]
     */
    private $currencies = [];

    /**
     * @param EntityManagerInterface $manager
     * @param Reader $reader
     */
    public function __construct(EntityManagerInterface $manager, Reader $reader)
    {
        $this->em = $manager;
        $this->reader = $reader;
        $this->loadCurrencies();
    }

    private function loadCurrencies(): void
    {
        foreach ($this->em->getRepository(Currency::class)
                     ->findAll() as $currency) {
            $this->currencies[$currency->getCode()] = $currency;
        }
    }

    /**
     * @return ArrayCollection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ReflectionException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getRates(): ArrayCollection
    {
        $repo = $this->em->getRepository(Exchange::class);

        //@formatter:off
        $rates = array_merge(
            $this->getBankRates($repo->findOneBy(['title' => 'bank']))->toArray(),
            $this->getFxexRates($repo->findOneBy(['title' => 'fxex']))->toArray(),
            $this->getFloatRates($repo->findOneBy(['title' => 'float']))->toArray(),
        );
        //@formatter:on

        return new ArrayCollection($rates);
    }

    /**
     * @param Exchange|null $exchange
     *
     * @return ArrayCollection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ReflectionException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getBankRates(?Exchange $exchange): ArrayCollection
    {
        $collection = new ArrayCollection();

        if (null !== $exchange && $feed = $this->reader->read($exchange->getUrl())) {
            foreach ($feed->getItems() as $item) {
                /** @var Element $item */
                if (true === $this->isDateAlreadySubmitted($item->getPubDate(), $exchange)) {
                    $values = explode(' ', trim($item->getDescription()));
                    $rates = [];
                    for ($i = 0; $i < (count($values)); $i += 2) {
                        $rates[$values[$i]] = (float)$values[$i + 1];
                    }

                    $this->save($collection, $exchange, $item->getPubDate(), $rates);
                }
            }
            $this->em->flush();
        }

        return $collection;
    }

    /**
     * @param DateTime $dateTime
     * @param Exchange $exchange
     * @param string|null $currency
     *
     * @return bool
     */
    private function isDateAlreadySubmitted(DateTime $dateTime, Exchange $exchange, ?string $currency = null): bool
    {
        return $this->em->getRepository(Rate::class)
            ->isDateAlreadySubmitted($dateTime, $exchange, $currency);
    }

    /**
     * @param ArrayCollection $collection
     * @param Exchange $exchange
     * @param DateTime $published
     * @param array $rates
     */
    private function save(ArrayCollection $collection, Exchange $exchange, DateTime $published, array $rates = []): void
    {
        foreach ($rates as $currency => $rate) {
            $rateObject = (new Rate())->setCurrency($this->getCurrency($currency))
                ->setPublishedAt($published)
                ->setRate($rate)
                ->setExchange($exchange);
            $this->em->persist($rateObject);
            $collection->add($rateObject);
        }
    }

    /**
     * @param string $currency
     *
     * @return Currency
     */
    private function getCurrency(string $currency): Currency
    {
        if (!isset($this->currencies[$currency])) {
            $currencyObject = (new Currency())->setCode($currency);
            $this->em->persist($currencyObject);
            $this->currencies[$currency] = $currencyObject;
        } else {
            $currencyObject = $this->currencies[$currency];
        }

        return $currencyObject;
    }

    /**
     * @param Exchange|null $exchange
     *
     * @return ArrayCollection
     * @throws ReflectionException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getFxexRates(?Exchange $exchange): ArrayCollection
    {
        $collection = new ArrayCollection();

        if (null !== $exchange && $feed = $this->reader->read($exchange->getUrl())) {
            $rates = [];
            $pubDate = null;
            foreach ($feed->getItems() as $item) {
                preg_match_all('/\(([^)]+)\)/', $item->getTitle(), $matches);
                if (true === $this->isDateAlreadySubmitted($item->getPubDate(), $exchange, $currency = $matches[1][1])) {
                    /** @var Element $item */
                    if (null === $pubDate) {
                        $pubDate = $item->getPubDate();
                    }
                    $rates[$currency] = Text::sanitizeFloat(str_replace('1 Euro = ', '', $item->getDescription()));
                }
            }
            if (null !== $pubDate) {
                $this->save($collection, $exchange, $pubDate, $rates);
            }

            $this->em->flush();
        }

        return $collection;
    }

    /**
     * @param Exchange|null $exchange
     *
     * @return ArrayCollection
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ReflectionException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFloatRates(?Exchange $exchange): ArrayCollection
    {
        $collection = new ArrayCollection();

        if (null !== $exchange && $feed = $this->reader->read($exchange->getUrl())) {
            $rates = [];
            $pubDate = null;
            foreach ($feed->getItems() as $item) {
                /** @var Element $item */
                if (true === $this->isDateAlreadySubmitted($item->getPubDate(), $exchange, $item->getTargetCurrency())) {
                    if (null === $pubDate) {
                        $pubDate = $item->getPubDate();
                    }
                    $rates[$item->getTargetCurrency()] = (float)$item->getExchangeRate();
                }
            }
            if (null !== $pubDate) {
                $this->save($collection, $exchange, $pubDate, $rates);
            }

            $this->em->flush();
        }

        return $collection;
    }
}
