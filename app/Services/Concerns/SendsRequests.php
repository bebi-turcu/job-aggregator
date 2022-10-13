<?php

declare(strict_types=1);

namespace App\Services\Concerns;

use App\Services\Enums\Method;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait SendsRequests
{
    public function getResponseBody(array $data = []): string
    {
        return $this->send(
            requestMethod: Method::GET,
            data: $data
        )->body();
    }

    public function send(
        string $requestMethod,
        array $data = [],
    ): Response
    {
        $request = $this->makeRequest();

        $response = $request->send(
            method: $requestMethod,
            url: $this->feed->uri,
            options: $data ? ['json' => $data] : [],
        );

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response;
    }

    protected function makeRequest(): PendingRequest
    {
        $request = Http::baseUrl(
            url: $this->feed->base_url,
        )->timeout(
            seconds: 15,
        )->withUserAgent(
            userAgent: 'Job Aggregator',
        );

        if ($this->feed->token) {
            $request->withToken(
                token: $this->feed->token,
            );
        }

        return $request;
    }
}
