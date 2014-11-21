<?php
namespace SimpleRoles\Controller;

/**
 * Users Controller Class
 *
 * PHP Version 5.3+
 *
 * LICENSE: Version 2.0 (the "License"); you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @category Users
 * @package SimpleRoles\Controller
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 * @copyright 2014 Brad Bonkoski
 * @link https://github.com/bradbonkoski/simpleRoles
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
