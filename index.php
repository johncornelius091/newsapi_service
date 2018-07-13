<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'HomeController.php';
require_once 'CachingController.php';
require 'vendor/autoload.php';

$container = new \Slim\Container;
$app = new \Slim\App($container);


$container['cache'] = function ($c) {
    $config = [
        'schema' => 'tcp',
        'host' => 'localhost',
        'port' => 6379,
        // other options
    ];
    $connection = new Predis\Client($config);
    return new Symfony\Component\Cache\Adapter\RedisAdapter($connection);
};

$app->get('/foo', function (Request $request, Response $response, array $args){
    $bar = new CachingController($this->get('cache'));
    return $bar->doSomething();
});


$app->get('/too', function (Request $request, Response $response, array $args){
    $bar = new HomeController();
    return $bar->home();
});


$app->get('/top-headlines', function (Request $request, Response $response, array $args) { 
	//print_r($request);	
	$body = $request->getQueryParams();

	#hit curl
	$url = 'https://newsapi.org/v2/top-headlines?country=' . strtolower($body['country']) . '&category=' . strtolower($body['category']) . '&apiKey=eadb6da4bb5847a8b5f5b8a633e53ab9';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result   

	// Fetch and return content, save it.
	$raw_data = curl_exec($ch);
	curl_close($ch);

	// If the API is JSON, use json_decode.
	$res_data = json_decode($raw_data, true);
	#frame our data
	foreach($res_data['articles'] as $article) {
		$output[] = array('News Title' => $article['title'], 'Description' => $article['description'], 'Source News Url' => $article['url'], 'Country' => $body['country'], 'Category' => $body['category'], 'Filter Keyword' => empty($body['keyword']) ? "" : $body['keyword']);
	}	

	$data = array("status" => "success", "message" => "Thank you for the request", "data"  => $output, "body" => $body,  "code" => 200);
	$response = $response->withHeader('Content-Type', 'application/json');
	$response = $response->withStatus(200);
	$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
	return $response;

});

/*$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    $response->getBody()->write("\n Thank you for requesting top-headlines");

    return $response;
});*/
$app->run();


/*require 'vendor/autoload.php';
$app = new Slim\App();

$app->get('/top-headlines', function() {
	echo "thank you for requesting top-headlines";
});

$app->run();*/
?>
