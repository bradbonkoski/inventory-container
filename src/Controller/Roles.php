<?php
namespace SimpleRoles\Controller;

/**
 * Package API Class
 *
 * @category SimpleRoles
 * @package package
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 */

use Silex\Application;
use SimpleRoles\Model\Users;
use Symfony\Component\HttpFoundation\Request;

/**
 * Package Class
 * @package package
 */
class Roles
{
    private $code;

    public function __construct()
    {
        $this->code = 200;
    }

    public function listRoles(Application $app, $pattern = null)
    {
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $res = $roleModel->listRoles($pattern);

        return $app->json($res, 200);
    }

    public function getUsers(Application $app, $role)
    {
        $ret = array();
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $userIds = $roleModel->getUserIdsForRole($role);

        $userModel = new Users($app['db']);
        if (count($userIds) > 0) {
            foreach ($userIds as $uid) {
                $ret[] = $userModel->getUserInfo($uid['user_id']);
            }
        } else {
            return $app->json(array(), 200);
        }

        return $app->json($ret, 200);
    }

    public function newRoles(Request $req, Application $app)
    {
        $ret = array();
        $roles = json_decode($req->getContent(), true);
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        foreach ($roles as $role) {
            $rid = $roleModel->roleExists($role['name']);
            if ($rid === false) {
                //Create a new Role
                 $rid = $roleModel->createNewRole($role['name'], $role['desc']);
            }
            if (is_numeric($rid) && $rid > 0) {
                $ret[] = array(
                    'id' => $rid,
                    'name' => $role['name'],
                    'desc' => $role['desc']
                );
            }
        }
        return $app->json($ret, 200);
    }
}
