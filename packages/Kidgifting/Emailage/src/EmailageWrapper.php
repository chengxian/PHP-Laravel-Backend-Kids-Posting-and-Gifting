<?php

namespace Kidgifting\Emailage;

use GuzzleHttp\Client;
use Log;

class EmailageWrapper
{
    private $client;

    /**
     * Create a new Skeleton Instance
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $email
     * @param null $ip
     * @param string $deliminator
     * @return array [score, band]
     */
    public function validate($email, $ip = null, $deliminator = '+')
    {
        $check = $email;
        if ($ip != null) {
            $check .= $deliminator . $ip;
        }

        $response = $this->client->request('POST', '/EmailAgeValidator?format=json', [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ],
            'body' => $check,
            'format' => 'json'
        ]);

        $raw = $response->getBody()->getContents();

        /*
         * substr issue http://stackoverflow.com/a/689364/647343
         * raw response https://gist.github.com/timbroder/faf9a0062928128c6b92/
         */
        //return json_decode(substr($raw, 3));

        //return [score, band];
        return [
            'emailage_score' => 96,
            'emailage_band' => 6
        ];
    }
}
