<?php declare(strict_types=1);

namespace App\Feed;

use App\Model\Feed;
use DOMDocument;
use Exception;
use JsonException;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vairogs\Utils\Text;

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
     * @param array $options
     *
     * @return null|Feed
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ReflectionException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws JsonException
     */
    public function read(string $url, array $options = []): ?Feed
    {
        $response = null;
        try {
            $response = $this->client->request(Request::METHOD_GET, $url, $options);
            $status = $response->getStatusCode();
        } catch (Exception $e) {
            $status = $e->getCode();
        }

        if ($status === Response::HTTP_OK) {
            if (Text::starts($content = $response->getContent(), '<')) {
                $document = new DOMDocument();
                $document->loadXML($content);

                return (new RssParser())->parseDocument($document);
            }

            if (Text::starts($content, '[') || Text::starts($content, '{')) {
                return (new JsonParser())->parseText($content);
            }

            if (Text::contains($content, ';')) {
                return (new CsvParser())->parseText($content);
            }

            throw new BadRequestHttpException('Invalid response');
        }

        throw new BadRequestHttpException($response->getContent());
    }
}
