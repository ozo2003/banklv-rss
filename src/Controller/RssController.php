<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\Rate;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RssController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws NonUniqueResultException
     * @Route(path="/", name="index", methods={Request::METHOD_GET})
     */
    public function index(Request $request): Response
    {
        $limit = 10;
        $em = $this->getDoctrine()->getManager()->getRepository(Rate::class);
        $options = [
            'page' => $page = abs((int)$request->query->get('page', 1)),
            'date' => $date = $request->query->get('date', 'latest'),
            'rates' => $em->findRates($date, $limit, ($page - 1) * $limit),
            'dates' => $em->getDates(),
        ];

        $count = $em->findRateCount($date);
        $options['max'] = (int)ceil($count / $limit);

        return $this->render('default.html.twig', $options);
    }
}
