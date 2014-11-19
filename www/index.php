<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Silex\Application();

$app->match('/', function() use($app) {
    $controller = new \H1ppo\Controller\Index($app['request'], new Response);
    return $controller->run();
});

$app->error(function (\Exception $e, $code) {
    $o = new \stdClass;
    $o->error = $e->getMessage();
    (new JsonResponse($o, $e->getCode()))->send();
    exit;
});

$app->run();
