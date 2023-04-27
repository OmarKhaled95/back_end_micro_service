<?php

namespace App\Http\Services;

class UserService extends ApiService
{
    public function __construct()
    {
      $this->endpoint  = env('USER_MICRO_SERVICE') . '/api';
    }
}
