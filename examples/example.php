<?php

use GuzzleHttp\Client;
use UxmanSarwar\Http;

require_once '../vendor/autoload.php';

$url = 'https://docs.guzzlephp.org';
$url = 'https://google.com';
$url = 'https://github.com';



// $client = \UxmanSarwar\Http::client();
// $r = $client->request('get', $url, [])->getBody()->getContents();
// $r = $client->get($url)->getBody()->getContents();

// \UxmanSarwar\Http::client();
// $r = \UxmanSarwar\Http::request('get', $url);


// var_dump(Http::cookieFilePath(''));


// var_dump($r);



$c = Http::client('./path');

// echo $c->get($url)->getBody()->getContents();

// print_r(Http::get($url));

print_r(Http::client());
