<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 11/22/16
 * Time: 3:27 PM
 */

namespace App\Generator;

/**
 * @param int $size
 * @return \Generator
 */
function generate(int $size): \Generator
{
    $randomData = str_repeat('RandomRandom', 512);

    while ($size > 0) {
        $length = min($size, strlen($randomData));
        $size -= $length;
        yield substr($randomData, 0, $length);
    }
}

/**
 * @param int $speed
 * @param \Generator $generator
 * @return \Generator
 * @throws \Exception
 */
function throttle(int $speed, \Generator $generator): \Generator
{
    if ($speed < 1024) {
        throw new \Exception("Minimal speed is 1024 bytes per second.");
    }

    $start = microtime(true);
    $bytes = $speed;

    foreach ($generator as $data) {
        $bps = $bytes / max(microtime(true) - $start, 1);

        $bytes += strlen($data);

        if ($bps > $speed) {
            $delta = $bps / $speed;
            usleep($delta * 1000000);
        }

        yield $data;
    }
}

/**
 * @param int $speed
 * @param \Generator $generator
 * @return \Generator
 * @throws \Exception
 */
function randomThrottle(\Generator $generator): \Generator
{
    foreach ($generator as $data) {
        usleep(rand(0, 500000));
        yield $data;
    }
}

/**
 * @param \Generator $generator
 */
function send(\Generator $generator)
{
    foreach ($generator as $data) {
        echo $data;
        flush();
    }
}
