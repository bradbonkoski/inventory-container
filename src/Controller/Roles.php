<?php
namespace SimpleRoles\Controller;

/**
 * Roles Controller Class
 *
 * @category SimpleRoles
 * @package package
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 */

use Silex\Application;
use SimpleRoles\Model\Users;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Roles
 * @package SimpleRoles\Controller
 */

class Roles
{
    /**
     * @param Application $app
     * @param null $pattern
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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

    /**
     * @param Application $app
     * @param $role
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteRole(Application $app, $role)
    {
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $roleModel->deleteRole($role);

        return $app->json(array(), 200);
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
                 $rid = $roleModel->createNewRole($role['name'], $role['description']);
            }
            if (is_numeric($rid) && $rid > 0) {
                $ret[$rid] = array(
                    'id' => $rid,
                    'name' => $role['name'],
                    'description' => $role['description']
                );
            }
        }
        return $app->json(array_values($ret), 200);
    }

    /**
     * @param Request $req
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addUserToRole(Request $req, Application $app)
    {
        $records = json_decode($req->getContent(), true);
        //get role id, get user id and assuming they both exist add it to the join table
        $ret = array();
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $userModel = new Users($app['db']);

        foreach($records as $record) {
            $rid = $roleModel->roleExists($record['role']);
            $userInfo = $userModel->getUserByUserName($record['user']);
            $uid = $userInfo['id'];
            try {
                $roleModel->addUserToRole($rid, $uid);
            } catch (\Exception $e) {
                $ret['error'][] = "{$record['role']} - {$record['user']} Failed : ".$e->getMessage();
                continue;
            }
            $ret['success'][] = "{$record['role']} - {$record['user']} Added";
        }

        return $app->json($ret, 200);
    }

    /**
     * @param Request $req
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeUser(Request $req, Application $app)
    {
        $records = json_decode($req->getContent(), true);
        //get role id, get user id and assuming they both exist add it to the join table
        $ret = array();
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $userModel = new Users($app['db']);

        foreach($records as $record) {
            $rid = $roleModel->roleExists($record['role']);
            $userInfo = $userModel->getUserByUserName($record['user']);
            $uid = $userInfo['id'];
            $roleModel->removeUserFromRole($rid, $uid);
            $ret['success'][] = "{$record['role']} - {$record['user']} Removed";
        }

        return $app->json($ret, 200);
    }
}
