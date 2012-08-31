<?php
class Amazon{

    private $data = [];
    private $url = null; 
    private $key = null;


    public function setData( $data , $url ){

        $this->data = [
                'SignatureMethod' => 'HmacSHA256',
                'SignatureVersion' => '2',
                'Timestamp' => $this->_getTimeFormat('now'),                ];
        $this->data += $data;
        $this->url  = $url;
        $this->key  = $this->data['Key'];
        unset( $this->data['Key'] );

        $this->data['CreatedAfter'] = $this-> _getTimeFormat( $this->data['CreatedAfter'] );
         
    }

    public function combine(){

        $url = parse_url( $this->url );
        uksort($this->data, 'strcmp');

        $sign = 'POST';
        $sign .= "\n";
        $sign .= $url['host'];
        $sign .= "\n";
        $sign .= '/Orders/2011-01-01';
        $sign .= "\n";
        $sign .= $this->_dataAsString( $this->data ); 

        $this->data['Signature'] = $this->_hash( $sign , $this->key );
    
        $this->data = $this->_dataAsString( $this->data );

        return $this->data; 

    }


    private function _dataAsString( $data ){
        $query = [];
        foreach ($data as $k => $v) {
            $query[] = $k . '=' . $this->_urlencode($v);
        }
        return implode('&', $query);
    }

    private function _getTimeFormat( $time ){
        $time =  new DateTime($time);
        return $time->format( DateTime::ISO8601 );
    }

    private function _hash( $sign,$key ){
        
         $hash = hash_hmac('sha256', $sign, $key, true);

         return base64_encode($hash);
    }

    private function _urlencode($url){
         return str_replace('%7E', '~', rawurlencode($url));
    }

}
