<?php

namespace App\Http\Services;

abstract class ApiService
{
    protected string $endpoint;

    public function request($method, $path, $data = [])
    {
        $response = \Http::withHeaders([
            'Authorization' => 'Bearer ' . request()->bearerToken()
        ])->$method("{$this->endpoint}/{$path}", $data);

        if ($response->ok()) {
            return   $response->json();
        }
        return ($response);
    }


    public function post($path, $data)
    {
        return $this->request('post', $path, $data);
    }

    public function get($path)
    {
        return $this->request('get', $path);
    }
}
