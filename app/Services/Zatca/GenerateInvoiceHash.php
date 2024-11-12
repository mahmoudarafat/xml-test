<?php
namespace App\Services\Zatca;

class GenerateInvoiceHash {

    private $invoice;

    public function __construct($xml){
        $this->xml = $xml;
    }

    /**
     *
     * Generate Invoice Binary Hash Start .
     *
     */
    public function GenerateBinaryHash(){
// dd($this->xml);
        return hash('sha256',$this->xml,true);

    }
    /**
     *
     * Generate Invoice Binary Hash End .
     *
     */

    /**
     *
     * Generate Invoice Binary Hash Encoded in Base64 Start .
     *
     */
    public function GenerateBinaryHashEncoded(){

        return base64_encode($this->GenerateBinaryHash());

    }
    /**
     *
     * Generate Invoice Binary Hash Encoded in Base64 End .
     *
     */

}
