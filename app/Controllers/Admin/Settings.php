<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    private string $configFile;

    public function __construct()
    {
        $this->configFile = WRITEPATH . 'store_config.json';
    }

    private function loadConfig(): array
    {
        if (!file_exists($this->configFile)) {
            return $this->defaultConfig();
        }
        $data = json_decode(file_get_contents($this->configFile), true);
        return array_merge($this->defaultConfig(), $data ?? []);
    }

    private function defaultConfig(): array
    {
        return [
            'store_name'    => 'Toko Roti',
            'store_tagline' => 'PT. Mimosa Tarte Indonesia',
            'store_address' => '',
            'store_phone'   => '',
            'store_email'   => '',
            'store_hours'   => 'Senin–Sabtu, 08.00–20.00 WIB',
            'store_instagram'  => '',
            'store_maps_embed' => '',
        ];
    }

    public function index()
    {
        return view('admin/settings/index', [
            'config' => $this->loadConfig(),
        ]);
    }

    public function save()
    {
        $fields = ['store_name','store_tagline','store_address','store_phone','store_email','store_hours','store_instagram','store_maps_embed'];
        $data   = [];
        foreach ($fields as $f) {
            $data[$f] = trim($this->request->getPost($f) ?? '');
        }

        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return redirect()->to('/admin/settings')->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
