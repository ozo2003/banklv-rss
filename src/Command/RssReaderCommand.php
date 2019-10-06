<?php declare(strict_types = 1);

namespace App\Command;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Feed\Reader;
use App\Model\Item;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function count;
use function explode;
use function getenv;
use function sprintf;
use function trim;

class RssReaderCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:rss:populate';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Currency[]
     */
    private $currencies = [];

    /**
     * @param Reader $reader
     * @param EntityManagerInterface $em
     */
    public function __construct(Reader $reader, EntityManagerInterface $em)
    {
        $this->reader = $reader;
        $this->em = $em;

        parent::__construct(self::$defaultName);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting rss population</info>');

        foreach ($this->em->getRepository(Currency::class)->findAll() as $currency) {
            $this->currencies[$currency->getCode()] = $currency;
        }

        $output->writeln('<fg=blue>Reading rss feed</>');

        try {
            $feed = $this->reader->read(getenv('RSS_URL'));
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return $e->getCode();
        }

        foreach ($feed->getItems() as $item) {
            /** @var Item $item */
            $pubDate = $item->getPubDate()->format('d.m.Y');

            $currentRate = $this->em->getRepository(Rate::class)->findOneBy(['publishedAt' => $item->getPubDate()]);
            if (null === $currentRate) {
                $values = explode(' ', trim($item->getDescription()));
                $rates = [];
                for ($i = 0; $i < (count($values)); $i += 2) {
                    $rates[$values[$i]] = (float)$values[$i + 1];
                }

                $output->writeln(sprintf('<fg=green>Inserting rates for %s into DB</>', $pubDate));
                foreach ($rates as $currency => $rate) {
                    if (!isset($this->currencies[$currency])) {
                        $output->writeln(sprintf('<fg=cyan>Inserting new currency: %s</>', $currency));
                        $currencyObject = (new Currency())->setCode($currency);
                        $this->em->persist($currencyObject);
                        $this->currencies[$currency] = $currencyObject;
                    } else {
                        $currencyObject = $this->currencies[$currency];
                    }

                    $rateObject = (new Rate())->setCurrency($currencyObject)->setPublishedAt($item->getPubDate())->setRate($rate);
                    $output->writeln(sprintf('<fg=blue>Inserting new rate for %s at %s</>', $currency, $pubDate));

                    $this->em->persist($rateObject);
                }
            } else {
                $output->writeln(sprintf('<comment>Rates for %s found in DB, skipping</comment>', $pubDate));
            }
        }
        $this->em->flush();

        $output->writeln('<info>Rss population finished</info>');

        return 0;
    }
}
