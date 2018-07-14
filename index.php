<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'HomeController.php';
require_once 'CachingController.php';
require_once 'ValidateController.php';
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

# handle default condition
$app->get('/', function (Request $request, Response $response, array $args){
	$data = array("status" => "error", "message" => "you are on wrong page", "data"  => [], "code" => 404);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus(404);
        $response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
        return $response;
});


$app->get('/top-headlines', function (Request $request, Response $response, array $args){
    # validate input data
    $validate = new ValidateController();
    $validator = $validate->validate($request);
    if ($validator['status'] == "error") {
	$data = array("status" => "error", "message" => $validator['message'], "data"  => $output, "code" => 400);
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus(400);
        $response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
        return $response;
    }
    # check in cache (if request is made within 10 minutes, return same data)
    $cache = new CachingController($this->get('cache'));
    $output = $cache->check($validator['data']);
    #return $bar->doSomething();
    if (!empty($output)) {
    	$data = array("status" => "success", "message" => "Thank you for the request, this is being served from cache", "data"  => $output,  "code" => 201);
    	$response = $response->withHeader('Content-Type', 'application/json');
    	$response = $response->withStatus(200);
    	$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
    	return $response;
    }

    # process the request
    $bar = new HomeController();
    $output = $bar->home($request, $response, $args, $validator['data']);
    
    if (!empty($output)) {
    	# add data to cache
    	$cache->add($validator['data'], $output);
    	# return output
    	$data = array("status" => "success", "message" => "Thank you for the request", "data"  => $output, "code" => 200);
    } else {
	$data = array("status" => "success", "message" => "No articles available for this request", "data"  => $output, "code" => 404);
    }
    $response = $response->withHeader('Content-Type', 'application/json');
    $response = $response->withStatus(200);
    $response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
    return $response;
});

$app->run();

?>
