<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ValidateController
{

   protected $countries = array("ae","ar","at","au","be","bg","br","ca","ch","cn","co","cu","cz","de","eg","fr","gb","gr","hk","hu","id","ie","il","in","it","jp","kr","lt","lv","ma","mx","my","ng","nl","no","nz","ph","pl","pt","ro","rs","ru","sa","se","sg","si","sk","th","tr","tw","ua","us","ve","za");

   protected $categories = array("business","entertainment","general","health","science","sports","technology");

   // constructor receives container instance
   public function __construct() {

   }

   public function foo() {
	echo "john";die;
   }

   public function validate(Request $request) {
	$body = $request->getQueryParams();
	if (empty($body['category'])) {
		# return from here
		return array("status" => "error", "message" => "category not found in the request", "data" => []);
	} else {
		$category = trim(strtolower($body['category']));
		if (in_array($category, $this->categories)) {
			$output['category'] = $category;
		} else {
			#return from here
			return array("status" => "error", "message" => "valid category not found in the request", "data" => []);
		}
	}

	if (empty($body['country'])) {
		# return from here
		return array("status" => "error", "message" => "country not found in the request", "data" => []);
	} else {
		$country = trim(strtolower($body['country']));
		if (in_array($country, $this->countries)) {
                        $output['country'] = $country;
                } else {
                        #return from here
			return array("status" => "error", "message" => "valid country not found in the request", "data" => []);
                }
	}

	if (!empty($body['keyword'])) {
		$output['keyword'] = trim(addslashes(strtolower($body['keyword'])));
	} else {
		$output['keyword'] = "";
	}

	$output['page_size'] = (empty($body['page_size'])) ? 20 : ((abs($body['page_size']) <= 100 && abs($body['page_size']) != 0) ? abs($body['page_size']) : 20);
	$output['page'] = (empty($body['page'])) ? 1 : ((abs($body['page']) != 0) ? abs($body['page']) : 1);
        return array("status" => "success", "message" => "proceed with processing", "data" => $output);
   }
}
