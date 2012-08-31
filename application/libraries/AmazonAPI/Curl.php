<?php

class Curl{

    private $param;
    private $ch;
    private $data = [];
    
    public function setParam($param){

         $postData[CURLOPT_URL] = $param['url']; 
         $postData[CURLOPT_POSTFIELDS] = $param['query'];
         $this->param = $postData;
    }

    private function _init(){
        $option = [
            CURLOPT_RETURNTRANSFER => true, // 返回数据 
            CURLOPT_POST => true,           
            CURLOPT_HTTP_VERSION => 1.1,
            CURLOPT_HEADER => false,       //不显示header
            CURLOPT_SSL_VERIFYPEER => true,
            // CURLOPT_HTTPHEADER => ['Content-Type: text/xml'],
            CURLOPT_USERAGENT => 'Plato/1.0 (Language=PHP)',
            
        ];
        $this->param += $option;
    }

    public function perform(){
        $this->_init();
        
        $this->ch = curl_init();
        curl_setopt_array($this->ch,$this->param);
        
        $this->data['data']     = curl_exec($this->ch);
        $this->data['httpcode'] = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE ); 

        if( curl_errno( $this->ch ) ) {
            $error =  curl_error($this->ch) ;
            throw new Exception( $error );
        }

        curl_close($this->ch);

        return $this->data;
    }
}
