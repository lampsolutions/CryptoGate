<?php

namespace App\Lib;


class CryptoGateBranding {

    public function __construct() {

    }

    public function getConfig() {
        // Build Branding Setup
        $config = (array) json_decode(\Cache::get('config'), true);

        $branding = [];
        foreach($config as $k => $v) {
            if(strpos($k, 'branding_') === 0) {
                $branding[$k]=$v;
            }
        }
        $branding_defaults = [
            'branding_seller_name' => 'CryptoGate',
            'branding_use_logo' => 'false',
            'branding_logo_uri' => '',
            'branding_logo_name' => '',
            'branding_logo_align' => 'left',
            'branding_primary_text_align' => 'left',
            'branding_primary_color' => '#eeeeee',
            'branding_primary_text_color' => '#000000',
            'branding_secondary_color' => '#2196F3',
            'branding_secondary_text_color' => '#ffffff'
        ];

        foreach($branding_defaults as $k => $default) {
            if(!isset($branding[$k])) $branding[$k] = $default;
        }

        return $branding;
    }

}
