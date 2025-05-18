<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CatApiService
{
    protected Client $client;
    protected string $baseUrl;
    protected ?string $apiKey;

    /**
     * Constructor del servicio.
     * Inicializa el cliente Guzzle y configura la URL base y la clave API.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.thecatapi.com/v1/';
        $this->apiKey = env('THE_CAT_API_KEY');

        $config = [
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'x-api-key' => $this->apiKey,
            ],
        ];

        $this->client = new Client($config);
    }

    /**
     * Obtiene una lista de razas de gatos desde TheCatAPI.
     *
     * @return array|null Retorna un array de razas o null en caso de error.
     */
    public function getBreeds(): ?array
    {
        try {
            $response = $this->client->request('GET', 'breeds');
            $body = $response->getBody();
            $breeds = json_decode($body, true);

            return $breeds;

        } catch (GuzzleException $e) {
            Log::error('Error fetching cat breeds from TheCatAPI: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
          Log::error('An unexpected error occurred while processing cat breeds API response: '
            . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene aleatoriamente la información de un gato
     *
     * @return array|null Retorna un array de razas o null en caso de error.
     */
    public function getRandomCat(): ?array
    {
        try {
            $response = $this->client->request('GET', 'images/search');
            $body = $response->getBody();
            $cat = json_decode($body, true);

            return $cat;
        } catch (GuzzleException $e) {
            Log::error('Error fetching cat breeds from TheCatAPI: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
          Log::error('An unexpected error occurred while processing cat breeds API response: '
            . $e->getMessage());
            return null;
        }
    }
}


