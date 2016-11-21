<?php

require __DIR__ . '/../vendor/autoload.php';

const LENGTH_LIMIT = 128000000;

$randomData = str_repeat('RandomRandom', 512);

$router = new \Nerd\Framework\Routing\Router();

$router->get('/', function () {
    require __DIR__ . '/../resources/view/home.html';
});

$router->get('(\d+)\.bin', function ($size) use ($randomData) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    $size = min($size, LENGTH_LIMIT);

    while ($size > 0) {
        $data = $size >= strlen($randomData) ? $randomData : substr($randomData, 0, $size);
        $size -= strlen($data);
        echo $data;
    }
});

$router->get('(\d+)\-(\d+)\.bin', function ($speed, $size) use ($randomData) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    $size = min($size, LENGTH_LIMIT);

    $start = microtime(true);
    $bytes = $speed;

    while ($size > 0) {
        $data = $size >= strlen($randomData) ? $randomData : substr($randomData, 0, $size);

        $bytes += strlen($data);
        $size -= strlen($data);

        $bps = $bytes / max(microtime(true) - $start, 1);

        if ($bps > $speed) {
            $delta = $bps / $speed;
            usleep($delta * 1000000);
        }

        echo $data;
    }
});

$router->get('random\-(\d+)\.bin', function ($size) use ($randomData) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment');

    $size = min($size, LENGTH_LIMIT);

    while ($size > 0) {
        $data = $size >= strlen($randomData) ? $randomData : substr($randomData, 0, $size);
        $size -= strlen($data);
        usleep(rand(0, 500000));
        echo $data;
    }
});

try {
    $router->run();
} catch (\Nerd\Framework\Routing\RouteNotFoundException $e) {
    http_response_code(404);
    echo $e->getMessage();
}

