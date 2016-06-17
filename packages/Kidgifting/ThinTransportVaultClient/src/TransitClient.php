<?php

namespace Kidgifting\ThinTransportVaultClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Log;

class TransitClient
{

    private $serverUrl;
    private $token;
    private $client;

    /**
     * TransportClient constructor.
     * @param string $serverUrl The vault server E.G. http://192.168.20.20:8200
     * @param string $token Token with the following (or more granular) access:
     *
     * path "transit/decrypt/*" {
     *   capabilities = ["create", "update"]
     * }
     *
     * path "transit/encrypt/*" {
     *  capabilities = ["create", "update"]
     * }
     *
     * also see vault.policy.web.json.example
     * @param null $client
     *
     */

    public function __construct($serverUrl, $token, ClientInterface $client = null)
    {
        $this->serverUrl = $serverUrl;
        $this->token = $token;
        if ($client == null) {
            $this->client = new Client([
                'base_uri' => $this->serverUrl,
                'timeout' => 2.0
            ]);
        } else {
            $this->client = $client;
        }

    }

    /**
     * @param $key
     * @param $plaintext
     * @param null $context
     * @return mixed
     * @throws StringException
     *
     * TODO PHP7 scalar type hinting
     */
    public function encrypt($key, $plaintext, $context = null)
    {
        if (!is_string($key)) {
            throw new StringException("\$key must be a string");
        }
        if (!is_string($plaintext)) {
            throw new StringException("\$plaintext must be a string");
        }
        if ($context !== null && !is_string($context)) {
            throw new StringException("\$context must be a string");
        }

        $url = '/transit/encrypt/' . $key;

        Log::debug("Encrypting");
        Log::debug([
            "key" => $key,
            "plaintext" => $plaintext,
            "context" => $context,
        ]);

        $data = $this->getEncryptPayload($key, $plaintext, $context);

        $response = $this->command($url, 'POST', $data);

        if ($response == null) {
            throw new VaultException("Empty response from Vault server");
        }

        if (!array_key_exists('data', $response)) {
            throw new VaultException("Vault Encrypt: data not returned");
        }

        if (!array_key_exists('ciphertext', $response['data'])) {
            throw new VaultException("Vault Encrypt: ciphertext not returned");
        }

        return $response['data']['ciphertext'];
    }

    /**
     * @param $key
     * @param $plaintext
     * @param null $context
     * @return array
     */
    protected function getEncryptPayload($key, $plaintext, $context = null)
    {
        $encoded = $this->encode($plaintext);

        Log::debug("encoded: $encoded");

        $data = ['plaintext' => $encoded];

        Log::debug('Enc ' . $key . ':' . $plaintext . ' as' . $encoded . "\n");


        if ($context) {
            $encodedContext = $this->encode($context);
            $data['context'] = $encodedContext;
        }

        return $data;
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function encode($string)
    {
        return base64_encode($string);
    }

    /**
     * @param $base64
     * @return mixed
     */
    protected function decode($base64)
    {
        return base64_decode($base64);
    }


    /**
     * @param $path
     * @param $cyphertext
     * @param null $context
     * @return mixed
     * @throws StringException
     *
     * TODO PHP7 scalar type hinting
     */
    public function decrypt($path, $cyphertext, $context = null)
    {
        if (!is_string($path)) {
            throw new StringException("\$path must be a string");
        }
        if (!is_string($cyphertext)) {
            throw new StringException("\$cyphertext must be a string");
        }
        if ($context !== null && !is_string($context)) {
            throw new StringException("\$context must be a string");
        }

        Log::debug("Decrypting");
        Log::debug([
            "path" => $path,
            "cyphertext" => $cyphertext,
            "context" => $context
        ]);

        $url = '/transit/decrypt/' . $path;
        $data = $this->getDecryptPayload($cyphertext, $context);

        $response = $this->command($url, 'POST', $data);

        $encoded = $response['data']['plaintext'];

        Log::debug("Encoded: $encoded");

        $plaintext = $this->decode($encoded);

        Log::debug("Plaintext: $plaintext");

        return $plaintext;
    }

    protected function getDecryptPayload($cyphertext, $context = null)
    {
        $data = ['ciphertext' => $cyphertext];

        if ($context) {
            $encodedContext = $this->encode($context);
            $data['context'] = $encodedContext;
        }

        return $data;
    }

    protected function getCommandPayload($payload)
    {
        $json = json_encode($payload);
        $payload = [
            'headers' => [
                'X-Vault-Token' => $this->token,
                'Content-Type' => 'application/json'
            ],
            'json' => $payload
        ];

        return $payload;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $payload
     * @return mixed
     * @throws GuzzleHttp\Exception\ClientException
     */
    private function command($url, $method = 'POST', $payload = [])
    {
        Log::debug($payload);

        $response = $this->client->request($method, 'v1' . $url,
            $this->getCommandPayload($payload)
        );

        return $this->parseResponse($response);
    }

    private function parseResponse($response)
    {
        return json_decode($response->getBody(), true);
    }
}
