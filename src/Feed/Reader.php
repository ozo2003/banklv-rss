<?php declare(strict_types = 1);

namespace App\Feed;

use App\Model\Feed;
use DOMDocument;
use Exception;
use ReflectionException;
use Symfony\Component\Config\Util\Exception\InvalidXmlException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vairogs\Utils\Text;
use function sprintf;

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
     * @return Feed|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ReflectionException
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
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

                return (new RssParser())->parse($document);
            }

            throw new InvalidXmlException(sprintf('Invalid xml received from %s', $url));
        }

        throw new BadRequestHttpException($response->getContent());
    }
}
