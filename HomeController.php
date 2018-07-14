<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class HomeController
{
   protected $container;

   // constructor receives container instance
   public function __construct() {
       //$this->container = $container;
   }

   public function home(Request $request, Response $response, $args, $body) {

        #hit curl
	$res_data = $this->_hit_api($body);
        #frame our data
	if (!empty($res_data['articles'])) {
        	foreach($res_data['articles'] as $article) {
                	$output[] = array('News Title' => $article['title'], 'Description' => $article['description'], 'Source News Url' => $article['url'], 'Country' => $body['country'], 'Category' => $body['category'], 'Filter Keyword' => empty($body['keyword']) ? "" : $body['keyword']);
        	}
        	return $output;
	} else {
		return array();
	}
   }

   protected function _hit_api($body) {
	$url = 'https://newsapi.org/v2/top-headlines?country=' . strtolower($body['country']) . '&category=' . strtolower($body['category']) . '&q=' .strtolower($body['keyword']). '&page=' .$body['page']. '&pageSize=' .$body['page_size']. '&apiKey=eadb6da4bb5847a8b5f5b8a633e53ab9';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result   

        // Fetch and return content, save it.
        $raw_data = curl_exec($ch);
        curl_close($ch);

        // If the API is JSON, use json_decode.
        $res_data = json_decode($raw_data, true);
	return $res_data;
   }

   public function send_output(Response $response, $data, $code = 200) {
	$response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus($code);
        $response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
        return $response;
   }
}
