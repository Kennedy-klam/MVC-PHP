<?php 

//echo "<pre>", var_dump($obResponse); echo "</pre>"; exit; 

require __DIR__.'/vendor/autoload.php';

use \App\Http\Router;
use \App\Http\Response;
use \App\Controller\Pages\Home;

define('URL', 'http://Localhost/mvc_php');

$obRouter = new Router(URL);

$obRouter->get('/', [
    function(){
        return new Response(200, Home::getHome());
    }
]);     

$obRouter->run()
         ->sendResponse();

