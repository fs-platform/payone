<?php

namespace Smbear\Payone\Services;

use Illuminate\Support\Facades\Http;
use Smbear\Payone\Exceptions\ParametersException;

class PayoneRequest
{
    /**
     * 发送请求
     * @param string $url
     * @param array $request
     * @return array|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendRequest(string $url,array $request)
    {
        if (empty($url) || empty($request)) {
            throw new ParametersException(__FUNCTION__.'：方法 参数异常');
        }

        ksort($request);
        
        $response = Http::withHeaders([
            'accept' => 'application/json'
        ])
            ->asForm()
            ->retry(3, 100)
            ->post($url,$request);

        if ($response->successful()) {
            return $response->json();
        }
        
        throw $response->throw();
    }
}