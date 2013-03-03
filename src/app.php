<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();

$app['mongo'] = $app->share(function () {
    return new \MongoClient();
});

$app->get('/', function () use ($app) {
    return file_get_contents(__DIR__.'/../web/index.html');
});

$app->get('/money.json', function(Request $request) use ($app) {
    $mongo = $app['mongo'];

    $skip = (int) $request->get('page', 0);
    $limit = 6;

    $results = $mongo->demo->money->aggregate([
        [ '$project' => [
            'minute' => (object)[
                '0' => [ '$year' => '$ts' ],
                '1' => [ '$month' => '$ts' ],
                '2' => [ '$dayOfMonth' => '$ts' ],
                '3' => [ '$hour' => '$ts' ],
                '4' => [ '$minute' => '$ts' ],
            ],
            'ts' => 1,
            'bid' => 1,
        ]],
        [ '$sort' => [ 'ts' => 1 ] ],
        [ '$group' => [
            '_id' => '$minute',
            'ts' => [ '$first' => '$ts' ],
            'bid_open' => [ '$first' => '$bid' ],
            'bid_close' => [ '$last' => '$bid' ],
            'bid_high' => [ '$max' => '$bid' ],
            'bid_low' => [ '$min' => '$bid' ],
            'bid_avg' => [ '$avg' => '$bid' ],
        ]],
        [ '$sort' => [ 'ts' => 1 ] ],
        [ '$skip' => $skip ],
        [ '$limit' => $limit ],
        [ '$project' => [
            '_id' => '$ts',
            'bid' => [
                'open' => '$bid_open',
                'close' => '$bid_close',
                'high' => '$bid_high',
                'low' => '$bid_low',
                'avg' => '$bid_avg',
            ]
        ]],
    ]);

    $results = $results['result'];

    // Convert MongoDate objects to ISO 8601
    foreach ($results as $k => $v) {
        $results[$k]['_id'] = date('c', $v['_id']->sec);
    }

    return $app->json($results);
});

return $app;
