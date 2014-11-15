<?php
namespace SimpleRoles\Controller;

/**
 * Users Controller
 *
 * @category SimpleRoles
 * @package package
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Users
 * @package SimpleRoles\Controller
 */

class Users
{
    /**
     * @param Request $req
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newUsers(Request $req, Application $app)
    {
        $ret = array();
        $users = json_decode($req->getContent(), true);
        $userModel = new \SimpleRoles\Model\Users($app['db']);

        foreach ($users as $user) {
            try {
                $uid = $userModel->addNewUser($user['name'], $user['username'], $user['ref']);
            } catch (\Exception $e) {
                $ret[$user['username']] = $e->getMessage();
                continue;
            }
            $ret[$user['username']] = array(
                'id' => $uid,
                'name' => $user['name'],
                'username' => $user['username'],
                'ref' => $user['ref']
            );
        }
        return $app->json(array_values($ret), 200);
    }
}
