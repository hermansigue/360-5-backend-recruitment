<?php

namespace App\Modules\Common\SafeRequest\Services;

class SafeRequestService {
    public function get($url, $params = [],$headers = []){
        if (!empty($params))  $url .= '?' . http_build_query($params);
        $contextOptions = $this->getContextOptions('GET', $headers);
        $context = stream_context_create($contextOptions);
        $response = file_get_contents($url, false, $context);

        if ($response === false) return "Error: Unable to fetch data.";
        $this->log($url, $headers, $params, $response);

        return $response;
    }

    public function post($url, $body = [], $headers = []) {
        $contextOptions = $this->getContextOptions('POST', $headers, $body);
        $context = stream_context_create($contextOptions);
    
        $response = file_get_contents($url, false, $context);
    
        if ($response === false) {
            return "Error: Unable to fetch data.";
        }    
        $this->log($url, $headers, $body, $response);
    
        return $response;
    }
    

    private function log($url, $headers, $params, $response){
        $logData = [
            'url' => $url,
            'headers' => $headers,
            'params' => $params,
            'response' => $response,
        ];
        $logDirectory = '../logs';
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        file_put_contents($logDirectory . '/request_log.txt', date("Y-m-d H:i:s") . " Request: " . json_encode($logData) . PHP_EOL, FILE_APPEND);

    }

    private function getContextOptions($method, $headers, $body=null){
        $keyHeaders = [];
        foreach ($headers as $key => $value) {
            $keyHeaders[] = "$key: $value";
        }
        $context = [
            'http' => [
                'method'  => strtoupper($method),
                'header'  => implode("\r\n", $keyHeaders),
                'ignore_errors' => true,
            ]
        ];
        if(in_array($method, ['PUT', 'POST'])){
            $context['http']['content'] = json_encode($body);
        }
        return $context;
    }
}