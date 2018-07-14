<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;

$app->get('/top-headlines', function (Request $request, Response $response, array $args) use($app)   { 
	//print_r($request);	
	return HomeController::home($app, $request, $response);
	/*$body = $request->getQueryParams();
	$data = array("status" => "success", "message" => "Thank you for the request", "data"  => $args, "body" => $body,  "code" => 200);
	$response = $response->withHeader('Content-Type', 'application/json');
	$response = $response->withStatus(200);
	$response = $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));*/
	#return $response;

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
