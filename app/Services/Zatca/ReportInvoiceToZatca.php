<?php
namespace App\Services\Zatca;
use App\Services\Zatca\ZatcaConfig;

class ReportInvoiceToZatca {

    private $company_setting;
    private $invoice;
    private $invoice_builder;
    private $certificate;
    private $secret;
    private $csid;
    private $CI;

    public function __construct($invoice_obj,$setting_obj){
        $this->company_setting = $setting_obj;
        $this->invoice = $invoice_obj;

        $this->invoice_builder = new \App\Services\Zatca\BuildInvoice($invoice_obj,$setting_obj);
        if($this->company_setting->is_production){

            $this->certificate = $this->company_setting->production_certificate;
            $this->secret = $this->company_setting->production_secret;
            $this->csid = $this->company_setting->production_csid;

        }else{

            $this->certificate = $this->company_setting->certificate;
            $this->secret = $this->company_setting->secret;
            $this->csid = $this->company_setting->csid;
            // dd(3);

        }

    }

    /**
     *
     *  Report Invoice Start .
     *
     */
    public function ReportInvoice(){

        // return $this->invoice_builder->GenerateInvoiceHash();
        $post = [
            'invoiceHash' => $this->invoice_builder->GenerateInvoiceHash(),
            'uuid' => $this->invoice->uuid,
            'invoice' => $this->invoice_builder->GenerateInvoiceXmlEncoded(),
        ];
        $url = '';
        if($this->company_setting->is_production){
            if($this->invoice->document_type == 'simplified'){
                $url = '/invoices/reporting/single';
            }else{
                $url = '/invoices/clearance/single';
            }
        }else{
            $url = '/compliance/invoices';
        }
        $client = new \GuzzleHttp\Client();

        try{

            $request = $client->request('POST',ZatcaConfig::BaseUrl().$url,[
                'json' => $post,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept-Language' => 'ar',
                    'Accept-Version' => 'V2',
                    'Clearance-Status' => '1',
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    $this->certificate, // username
                    $this->secret // password
                ]
            ]);

            $response = $request->getBody()->getContents();

            $response_encode = json_decode($response);
            if($this->invoice->document_type == 'standard'){
                $xml = (isset($response_encode->clearedInvoice)) ? $response_encode->clearedInvoice : $this->invoice_builder->GenerateInvoiceXmlEncoded();
            }else{
                $xml = $this->invoice_builder->GenerateInvoiceXmlEncoded();
            }

            $data = [];
            $data['hash'] = $this->invoice_builder->GenerateInvoiceHash();
            $data['sent_to_zatca'] = true;
            $data['sent_to_zatca_status'] = 'PASS';
            $data['signing_time'] = $this->invoice_builder->signing_time;
            $data['xml'] = $xml;
            return ['success' => true,'message' => $response_encode , 'qr_code' => (string)$this->invoice_builder->GetQrCodeFromXml($xml) , 'data' => $data];
        }
        catch(\Exception $e){
            $response = $e->getResponse();
            $response = $response->getBody()->getContents();
            return $response ;
            $response_encode = json_decode($response);
          return $response_encode;
            dd($response , 587);

            return ['success' => false,'message' => $response_encode];
        }

    }
}
