<?php

require_once 'bootstrap.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

$dbh = new \SimpleRoles\DB();
$app['db'] = $dbh->getConn();

$app->after(function (Request $request, Response $response) {
    $response->headers->set('X-ws', gethostname());
});

//GET Requests
$app->get('/roles', 'SimpleRoles\Controller\Roles::listRoles');
$app->get('/roles/{pattern}', 'SimpleRoles\Controller\Roles::listRoles');

$app->get('/users/{role}', 'SimpleRoles\Controller\Roles::getUsers');

//POST - CREATES
$app->post('/roles', 'SimpleRoles\Controller\Roles::newRoles');

/* Default Catch All Route */
$app->match(
    '{url}',
    function ($url) use ($app) {
        return $app->json(array(), 404);
    }
)->assert('url', '.+');

$app->run();
