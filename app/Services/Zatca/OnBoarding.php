<?php
namespace App\Services\Zatca;

use App\Models\ZatcaSetting;
use App\Services\Zatca\ZatcaConfig;
use Illuminate\Support\Facades\DB;

class OnBoarding {

    private $taxPayerConfig;
    private $CI;

    public function __construct($taxPayerConfig){

        $this->taxPayerConfig = $taxPayerConfig;
        // call generateConfigFile Method to generate config file for init step .
        $this->generateConfigFile();

    }

    /**
     *
     * generate config file start
     *
     */
    public function generateConfigFile(){
        if($this->taxPayerConfig->is_production){
            return $this;
        }
        $certificateTemplateName = 'TSTZATCA-Code-Signing';
        $config_file = "
        oid_section = OIDs
        [ OIDs ]
        certificateTemplateName= 1.3.6.1.4.1.311.20.2

        [ req ]
        default_bits 	= 2048
        emailAddress 	= {$this->taxPayerConfig->email_address}
        req_extensions	= v3_req
        x509_extensions 	= v3_ca
        prompt = no
        default_md = sha256
        req_extensions = req_ext
        distinguished_name = dn

        [ v3_req ]
        basicConstraints = CA:FALSE
        keyUsage = digitalSignature, nonRepudiation, keyEncipherment

        [req_ext]
        certificateTemplateName = ASN1:PRINTABLESTRING:{$certificateTemplateName}
        subjectAltName = dirName:alt_names

        [ v3_ca ]


        # Extensions for a typical CA


        # PKIX recommendation.

        subjectKeyIdentifier=hash

        authorityKeyIdentifier=keyid:always,issuer:always
        [ dn ]
        CN ={$this->taxPayerConfig->common_name}  				                    # Common Name
        C={$this->taxPayerConfig->country_name}							            # Country Code e.g SA
        OU={$this->taxPayerConfig->organization_unit_name}							# Organization Unit Name
        O={$this->taxPayerConfig->organization_name}							    # Organization Name

        [alt_names]
        SN={$this->taxPayerConfig->egs_serial_number}				                # EGS Serial Number 1-ABC|2-PQR|3-XYZ
        UID={$this->taxPayerConfig->trn}						                    # Organization Identifier (VAT Number)
        title={$this->taxPayerConfig->invoice_type}								    # Invoice Type
        registeredAddress={$this->taxPayerConfig->registered_address}  	 			# Address
        businessCategory={$this->taxPayerConfig->business_category}					# Business Category";

        $this->taxPayerConfig->cnf = base64_encode($config_file);
    }
    /**
     *
     * generate config file end
     *
     */

    /**
     *
     * generate csr request file end
     *
     */
    public function generatePemsKeys(){


        $zatcaSetting = ZatcaSetting::first();
        if($this->taxPayerConfig->is_production){
            return $this;
        }
        // convert config column to temp file start
        $temp = tmpfile();

        fwrite($temp, base64_decode($this->taxPayerConfig->cnf));
        fseek($temp, 0);
        $tmpfile_path = stream_get_meta_data($temp)['uri'];
        $file_cnf = file_get_contents($tmpfile_path);
        // convert config column to temp file end


        $config = [
            "config" => $tmpfile_path,
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'secp256k1'
        ];
        $res = openssl_pkey_new($config);

        if (!$res) {
            echo 'ERROR: Fail to generate private key. -> ' . openssl_error_string();
            exit;
        }

        $setting = DB::table('zatca_settings')->where('shop_id',132)->first();
        $priv_key =$setting->private_key;
        // Generate Private Key and Store it start
        openssl_pkey_export($res, $priv_key , NULL, $config);


        $data = [];
        $zatcaSetting->update([
            'private_key'=>base64_encode($priv_key)
        ]);
        $this->taxPayerConfig->private_key = base64_encode($priv_key);



        $key_detail = openssl_pkey_get_details($res);
        $pub_key = $key_detail["key"];

        $data = [];
        $this->taxPayerConfig->public_key = base64_encode($pub_key);
        // Get The Public Key and Store it end
        $zatcaSetting->update([
            'public_key'=>base64_encode($pub_key)
        ]);
        $dn = [
            "commonName" => $this->taxPayerConfig->common_name,
            "organizationalUnitName" => $this->taxPayerConfig->organization_unit_name,
            "organizationName" => $this->taxPayerConfig->organization_name??"test",
            "countryName" => 'SA'
        ];
        // dd($dn);
        $priv_key =$setting->private_key;
        $csr = openssl_csr_new($dn, $priv_key, array('digest_alg' => 'sha256' ,"req_extensions" => "req_ext",'curve_name' => 'secp256k1',"config" => $tmpfile_path));
// dd($csr);

        openssl_csr_export($csr,$csr_string);

        $data = [];
        $this->taxPayerConfig->csr_request = base64_encode($csr_string);
        // Generate a certificate signing request end
        $zatcaSetting->update([
            'csr_request'=>base64_encode($csr_string)
        ]);
        fclose($temp); // this removes the file
        // return same object
        return $this;
    }
    /**
     *
     * generate csr request file end
     *
     */

    /**
     *
     * generate x509 certificate from Zatca API'S start
     *
     */
    public function Cert509($type){
        // set post fields

        if($type == 'production'){
            $post = [
                'compliance_request_id' => $this->taxPayerConfig->csid,
            ];
        }elseif($type == 'compliance'){
            $post = [
                'csr' => $this->taxPayerConfig->csr_request,
            ];
        }



        $url = ($type == 'production') ? '/production/csids' : '/compliance';
        $client = new \GuzzleHttp\Client();
         try{

            $request = $client->request('POST',ZatcaConfig::BaseUrl().$url,[
                'json' => $post,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'otp' => $this->taxPayerConfig->otp,
                    'Accept-Version' => 'V2',
                    'Accept' => 'application/json'
                ],
                'auth' => [
                    $this->taxPayerConfig->certificate, // username
                    $this->taxPayerConfig->secret // password
                ]
            ]);



            $response = $request->getBody()->getContents();
            
            $response = json_decode($response);
            $certificate = ($type == 'compliance') ? 'certificate' : 'production_certificate';
            $secret = ($type == 'compliance') ? 'secret' : 'production_secret';
            $csid = ($type == 'compliance') ? 'csid' : 'production_csid';

            $data = [];
            $data['cnf'] = $this->taxPayerConfig->cnf;
            $data['private_key'] = $this->taxPayerConfig->private_key;
            $data['public_key'] = $this->taxPayerConfig->public_key;
            $data['csr_request'] = $this->taxPayerConfig->csr_request;
            $data[$certificate] = $response->binarySecurityToken;
            $data[$secret] = $response->secret;
            $data[$csid] = $response->requestID;

            return ['success' => true,'message' => $response->dispositionMessage , 'data' => $data];
         }
         catch(\Exception $e){
             $response = $e->getResponse();
             $response_source = $response->getBody()->getContents();
             $response = json_decode($response_source);
             if(isset($response->errors) && count($response->errors) > 0){
                 return ['success' => false,'errors' => $response->errors];
             }
             elseif(isset($response->code) && $response->code == 'Invalid-OTP'){
                 return ['success' => false,'errors' => [$response->message]];
             }
             elseif(isset($response->code) && $response->code == 'Missing-ComplianceSteps'){
                 return ['success' => false,'errors' => [$response->message]];
             }else{
                 return ['success' => false,'errors' => [$response_source]];
             }
         }

    }
    /**
     *
     * generate x509 certificate from Zatca API'S end
     *
     */

    /**
     *
     * issue x509 certificate end
     *
     */
    public function IssueCert509(){
        if($this->taxPayerConfig->is_production){
            return $this->Cert509('production');
        }else{
            return $this->Cert509('compliance');
        }
    }
    /**
     *
     * issue x509 certificate end
     *
     */

}
