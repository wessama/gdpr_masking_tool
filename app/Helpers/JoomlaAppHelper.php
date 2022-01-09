<?php

namespace App\Helpers;

class JoomlaAppHelper implements IWebservice {

    private $app;
    private $option;
    private $resource;
    private $format;

    public function __construct()
    {
        $this->app      = "anonymize";
        $this->option   = "com_api";
        $this->resource = "dispatch";
        $this->format   = "raw";
    }

	public function prepareAPIRequest($instance, $emails = "", $is_mass_request = false) {
        $response          = new \stdClass();
        $response->code    = '';
        $response->message = '';

        $client = new \GuzzleHttp\Client(['base_uri' => $instance->url]);

        try {

            $request = $client->request('POST', $instance->url . "/index.php", [
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'query'       => [
                    'option'   => $this->option,
                    'app'      => $this->app,
                    'resource' => $this->resource,
                    'format'   => $this->format,
                    'key'      => $instance->key,
                ],
                'form_params' => [
                    'emails'       => $emails,
                    'client_name'  => $instance->name,
                    'mass_request' => $is_mass_request,
                ],
            ]);

            $response = json_decode($request->getBody());

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