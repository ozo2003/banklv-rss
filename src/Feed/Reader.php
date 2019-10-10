<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Feed;
use DOMDocument;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Reader
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     *
     * @return Feed
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function read(string $url): Feed
    {
        $response = null;
        try {
            $response = $this->client->request(Request::METHOD_GET, $url);
            $status = $response->getStatusCode();
        } catch (Exception $e) {
            $status = $e->getCode();
        }

        if ($status === Response::HTTP_OK) {
            $document = new DOMDocument();
            $document->loadXML($response->getContent());

            return (new Parser())->parse($document);
        }

        throw new BadRequestHttpException($response->getContent());
    }
}
