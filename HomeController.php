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

	//$body = $request->getQueryParams();
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

   public function contact($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');
        return $response;
   }
}

/*class HomeController
{
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }
    public function home($request, $response, $args) {
      // your code here
      // use $this->view to render the HTML
     $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    $response->getBody()->write("\n Thank you for requesting top-headlines");
      return $response;
    }
}

*/
