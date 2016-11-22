<?php

use function App\Generator\generate;
use function App\Generator\randomThrottle;
use function App\Generator\send;
use function App\Generator\throttle;

require __DIR__ . '/../vendor/autoload.php';

const LENGTH_LIMIT = 128 * 1024 * 1024;

$router = new \Nerd\Framework\Routing\Router();

$router->get('/', function () {
    $revision = file_get_contents('../.revision');
    require __DIR__ . '/../resources/view/home.phtml';
});

$router->get('(\d+)\.bin', function ($size) {
    $limitedSize = min($size, LENGTH_LIMIT);

    $generator = generate($limitedSize);

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    send($generator);
});

$router->get('(\d+)\-(\d+)\.bin', function ($speed, $size) {
    $limitedSize = min($size, LENGTH_LIMIT);

    $generator = throttle($speed, generate($limitedSize));

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    send($generator);
});

$router->get('random\-(\d+)\.bin', function ($size) {
    $limitedSize = min($size, LENGTH_LIMIT);

    $generator = randomThrottle(generate($limitedSize));

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    send($generator);
});

try {
    $router->run();
} catch (\Nerd\Framework\Routing\RouteNotFoundException $e) {
    http_response_code(404);
    echo $e->getMessage();
} catch (\Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
