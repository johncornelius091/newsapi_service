<?php


class HomeController
{
   protected $container;

   // constructor receives container instance
   public function __construct(ContainerInterface $container) {
       $this->container = $container;
   }

   public function home($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');
        return $response;
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
