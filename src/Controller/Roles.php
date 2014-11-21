<?php
namespace SimpleRoles\Controller;

/**
 * Roles Controller Class
 *
 * PHP Version 5.3+
 *
 * LICENSE: Version 2.0 (the "License"); you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @category Roles
 * @package SimpleRoles\Controller
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 * @copyright 2014 Brad Bonkoski
 * @link https://github.com/bradbonkoski/simpleRoles
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

    /**
     * Controller Method to create New Roles
     *
     * @param Request $req
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
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

    /**
     * @param Application $app
     * @param $user
     * @param $role
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function userInRole(Application $app, $user, $role)
    {
        $roleModel = new \SimpleRoles\Model\Roles($app['db']);
        $userModel = new Users($app['db']);

        $roleInfo = $roleModel->getUserIdsForRole($role);
        $userInfo = $userModel->getUserByUserName($user);
        $uid = $userInfo['id'];

        foreach($roleInfo as $r) {
            if ($r['user_id'] == $uid) {
                return $app->json(array('msg' => "User In Role"), 200);
            }
        }
        return $app->json(array('msg' => "No User Found"), 404);
    }
}
