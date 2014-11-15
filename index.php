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
// Check is a user is in a given role
$app->get('/roles/{user}/{role}', 'SimpleRoles\Controller\Roles::userInRole');

$app->get('/users/{role}', 'SimpleRoles\Controller\Roles::getUsers');


//POST - CREATES
$app->post('/roles', 'SimpleRoles\Controller\Roles::newRoles'); //creates new role
$app->post('/users', 'SimpleRoles\Controller\Users::newUsers'); //creates new user

$app->put('/roles', 'SimpleRoles\Controller\Roles::addUserToRole'); //add user to a role

//delete a role
$app->delete('/roles', 'SimpleRoles\Controller\Roles::removeUser'); //removes user(s) from role(s)

$app->delete('/role/{role}', 'SimpleRoles\Controller\Roles::deleteRole'); //deletes a role

/* Default Catch All Route */
$app->match(
    '{url}',
    function ($url) use ($app) {
        return $app->json(array(), 404);
    }
)->assert('url', '.+');

$app->run();
