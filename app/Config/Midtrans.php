<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Midtrans extends BaseConfig
{
    public bool   $isProduction = false;
    public string $serverKey    = '';
    public string $clientKey    = '';

    public function __construct()
    {
        parent::__construct();
        $this->serverKey    = env('MIDTRANS_SERVER_KEY', '');
        $this->clientKey    = env('MIDTRANS_CLIENT_KEY', '');
        $this->isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
    }
}
