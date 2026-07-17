<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function about()
    {
        $configFile = WRITEPATH . 'store_config.json';
        $config = file_exists($configFile)
            ? (json_decode(file_get_contents($configFile), true) ?? [])
            : [];

        return view('about', ['config' => $config]);
    }

    public function faq()
    {
        $configFile = WRITEPATH . 'store_config.json';
        $config = file_exists($configFile)
            ? (json_decode(file_get_contents($configFile), true) ?? [])
            : [];

        return view('faq', ['config' => $config]);
    }

    public function contact()
    {
        return redirect()->to('/about#kontak');
    }
}
