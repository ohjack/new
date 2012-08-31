<?php



include('Amazon.php');
include('Curl.php');
$curl = new Curl();

$postData = [
        'Version' => '2011-01-01',
        'AWSAccessKeyId' => 'AKIAJGUMF5LENLIW6ZAQ',
        'SellerId' => 'A3LMXTNFZ71A3Q',
        'Action' => 'ListOrders',
        'MarketplaceId.Id.1' => 'ATVPDKIKX0DER',
        'CreatedAfter' => '2012-08-21 00:00:00',
        'Key' => 'jRa5CBIrZVTMm+GD9wwSNSQ+vwpyflw1eUn6aebL',
        ];

$url = 'https://mws.amazonservices.com/Orders/2011-01-01';
$amazon = new Amazon();
$amazon -> setData( $postData,$url );
$data = $amazon -> combine();
    
$param = [
    'url' => $url, 
    'query' => $data,    
];

$map = [
    200 => '',
    500 => 'retry',
    503 => 'retry',
    ];

$curl -> setParam($param);
$data = $curl -> perform();
print_r( $data );
