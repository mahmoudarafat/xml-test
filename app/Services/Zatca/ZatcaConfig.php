<?php
namespace App\Services\Zatca;

class ZatcaConfig {

    private static $base_url = 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal';
    //private static $base_url = 'https://gw-apic-gov.gazt.gov.sa/e-invoicing/simulation';

    public static function BaseUrl(){

        return self::$base_url;

    }
}
