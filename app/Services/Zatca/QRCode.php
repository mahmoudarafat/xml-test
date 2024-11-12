<?php
namespace App\Services\Zatca;

class QRCode {

    private $result;

    public function __construct($params){

        foreach($params as $key=>$value){

            $tag = $key+1;
            $length = $this->stringLen($value);
            $this->result .= $this->toString($tag,$length,$value);

        }
    }

    /**
     *
     *  @return the number of bytes start .
     *
     */
    public function stringLen($string){

        return strlen($string);

    }
    /**
     *
     *  @return the number of bytes end .
     *
     */

    /**
     *
     *  @param $tag , $length , $value
     *
     *  @return string returns a string representing the encoded TLV data structure start .
     *
     */
    public function toString($tag,$length,$value){

        return $this->__toHex($tag).$this->__toHex($length).($value);

    }
    /**
     *
     *  @return string returns a string representing the encoded TLV data structure end .
     *
     */

    /**
     * to convert the string value to hex start
     *
     * @param $value
     *
     * @return false|string
     */
    public function __toHex($value) {

        return pack("H*", sprintf("%02X", $value));

    }
    /**
     * to convert the string value to hex end
     *
     * @param $value
     *
     * @return false|string
     */

    public function getResult(){

        return $this->result;

    }
    /**
     * to convert qr value to base64 encode start
     *
     *
     * @return Qrcode value represented in base64 encoding
     */
    public function toBase64(){

        return base64_encode($this->result);

    }
    /**
     * to convert qr value to base64 encode end
     *
     *
     * @return Qrcode value represented in base64 encoding
     */
}
