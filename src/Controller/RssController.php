<?php declare(strict_types = 1);

namespace App\Controller;

use App\Feed\RateFacade;
use App\Feed\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function dump;

class RssController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={Request::METHOD_GET})
     */
    public function index(Reader $reader, EntityManagerInterface $manager)
    {
        //try {
        //    //$feed = $reader->read('https://www.bank.lv/vk/ecb_rss.xml');
        $feed2 = $reader->read('https://www.snb.ch/selector/en/mmr/exfeed/rss', ['timeout' => 30]);
        //    //$feed3 = $reader->read('https://eur.fxexchangerate.com/rss.xml');
        //} catch (Exception $e){
        //
        //}
        //$feed4 = $reader->read('http://www.floatrates.com/daily/eur.xml');
        //
        //if($feed4) {
        //    foreach ($feed4->getItems() as $item) {
        //        /** @var Element $item */
        //        dump($item->getDescription());
        //    }
        //}

        $facade = new RateFacade($manager, $reader);
        dump($facade->getRates());

        exit;
    }
}
