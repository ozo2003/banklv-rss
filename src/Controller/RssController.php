<?php declare(strict_types = 1);

namespace App\Controller;

use App\Feed\Reader;
use App\Model\Element;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function dump;
use function getenv;

class RssController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={Request::METHOD_GET})
     */
    public function index(Reader $reader)
    {
        $feed = $reader->read(getenv('RSS_URL'));
        $feed2 = $reader->read('https://www.snb.ch/selector/en/mmr/exfeed/rss');
        foreach ($feed->getItems() as $item) {
            /** @var Element $item */
            dump($item->getElement('description'));
        }
        exit;
    }
}
