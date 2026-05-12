<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function about()
    {
        return view('about');
    }

    public function faq()
    {
        return view('faq');
    }

    public function contact()
    {
        $configFile = WRITEPATH . 'store_config.json';
        $config = [];
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true) ?? [];
        }

        return view('contact', ['config' => $config]);
    }
}
