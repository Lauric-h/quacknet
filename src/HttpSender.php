<?php


namespace App;


use App\Entity\Quack;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
//use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Class HttpSender
 * @package App
 */
class HttpSender
{
    // define env variable for server
    // send new
    // send update
    // send delete rq
    // handle error

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;
//    private Serializer $serializer;

    /**
     * HttpSender constructor.
     */
    public function __construct() {
        $this->client = HttpClient::create();
        // need to resolve serialization pb
        //$this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

//    public function getEsId(int $id): string {
//        $response = $this->client->request(
//            'GET',
//            'http://localhost:9200/quack/_doc/q='
//        );
//    }

    /**
     * @param $data
     * @param $method
     * @return int response http status code
     * @throws TransportExceptionInterface
     */
    public function sendData($data): int
    {
        $formattedData = $this->formatData($data);

        $response = $this->client->request(
            'POST',
            'http://localhost:9200/quack/_doc/'.$data->getId(),
            [
                'json' => $formattedData
            ]
        );

        return $response->getStatusCode();
    }

//    public function deleteData($id) {
//
//    }

    public function updateData($data): int
    {
        $formattedData = $this->formatData($data);

        $response = $this->client->request(
            'POST',
            'http://localhost:9200/quack/_update/'.$data->getID(),
            [
                'json' => $formattedData
            ]
        );
        dd($response);
        return $response->getStatusCode();
    }

    // manual formatting as long as serialization not solved
    private function formatData(Quack $data): array {
        return [
            "id" => $data->getId(),
            "content" => $data->getContent(),
            "created_at" => $data->getCreatedAt(),
            "photo" => $data->getPhoto(),
            "duck" => $data->getDuck()->getUsername(),
            "deleted" => $data->getDeleted(),
            "positive" => $data->getPositive(),
            "negative" => $data->getNegative(),
            "updated_at" => $data->getUpdatedAt(),
        ];
    }
 }