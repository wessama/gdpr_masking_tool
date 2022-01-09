<?php

namespace App\Helpers;

class MoodleAppHelper implements IWebservice
{

    private $wsfunction;
    private $moodlewsrestformat;

    public function __construct()
    {
        $this->wsfunction         = "local_leap_user";
        $this->moodlewsrestformat = "json";
    }

    public function prepareAPIRequest($instance, $emails = "", $is_mass_request = false)
    {
        $response          = new \stdClass();
        $response->code    = '';
        $response->message = '';

        $request = array(
            'client'                  => $instance->name,
            'mass_request'            => $is_mass_request,
            'emails'                  => $emails,
            'action'                  => "mask_user");

        $client = new \GuzzleHttp\Client(['base_uri' => $instance->url]);

        try {

            $request = $client->request('GET', $instance->url . "/webservice/rest/server.php", [
                'query' => [
                    'wstoken'            => $instance->key,
                    'wsfunction'         => $this->wsfunction,
                    'moodlewsrestformat' => $this->moodlewsrestformat,
                    'request'            => json_encode($request),
                ],
            ]);

            $response = json_decode($request->getBody());

            $response = json_decode($response->data);

        } catch (RequestException $e) {

            if ($e->getResponse()->getStatusCode() == Response::HTTP_BAD_REQUEST) {
                $response = json_decode($e->getResponse()->getBody());
            } else {
                $response->code    = $e->getResponse()->getStatusCode();
                $response->message = $e->getMessage();
            }

            return $response;
        }

        return $response;
    }

}
