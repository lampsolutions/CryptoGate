<?php

namespace App\Lib\Electrum;

class RequestFactory {
    /**
     * @var array
     */
    private $nonces = [];

    /**
     * @param string $method
     * @param array $params
     * @return Request
     */
    public function create($method, $params = array()){
        do {
            $id = mt_rand(0, PHP_INT_MAX);
        } while (in_array($id, $this->nonces));

        return new Request($id, $method, $params);
    }

    /**
     * @param string $string
     * @return Response|Request|\Exception
     * @throws \Exception
     */
    public function response($string) {
        $decoded = json_decode(trim($string), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $id = isset($decoded['id']) ? $decoded['id'] : null;

            if (isset($decoded['error'])) {
                \Log::info("Got Electrum Error", $decoded);
                return new ErrorResponse($id, json_encode($decoded['error']));
            } elseif (isset($decoded['method']) && isset($decoded['params'])) {
                return new Request($id, $decoded['method'], $decoded['params']);
            } elseif (isset($decoded['result'])) {
                return new Response($id, $decoded['result']);
            } elseif ( isset($decoded['id']) && is_null($decoded['result']) ) {
                return new Response($id, null);
            }

            throw new \Exception('Response missing error/params/result');
        }

        throw new \Exception('Invalid JSON');
    }
}