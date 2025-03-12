<?php 

//echo "<pre>", var_dump($obResponse); echo "</pre>"; exit; 

require __DIR__.'/vendor/autoload.php';

use \App\Http\Router;
use \App\Utils\View;

define('URL', 'http://Localhost/mvc_php');

View::init([
    'URL' => URL
]);

$obRouter = new Router(URL);

include __DIR__.'/routes/pages.php';

$obRouter->run()
         ->sendResponse();

