<?php declare(strict_types = 1);

namespace App\Command;

use App\Feed\RateFacade;
use App\Feed\Reader;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function sprintf;

class RssCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:rss:populate';

    /**
     * @var RateFacade
     */
    private $facade;

    /**
     * @param Reader $reader
     * @param EntityManagerInterface $em
     */
    public function __construct(Reader $reader, EntityManagerInterface $em)
    {
        $this->facade = new RateFacade($em, $reader);

        parent::__construct(self::$defaultName);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ReflectionException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting rss population</info>');
        $output->writeln('<fg=blue>Reading rss feed</>');
        $output->writeln(sprintf('%d new rates imported', $this->facade->getRates()
            ->count()));
        $output->writeln('<info>Rss population finished</info>');

        return 0;
    }
}
