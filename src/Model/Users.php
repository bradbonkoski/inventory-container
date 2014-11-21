<?php
namespace SimpleRoles\Model;

/**
 * Users Model Class
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
 * @package SimpleRoles\Model
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 * @copyright 2014 Brad Bonkoski
 * @link https://github.com/bradbonkoski/simpleRoles
 */

class Users
{
    private $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getUserInfo($uid)
    {
        $sql = "select * from users where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id' => $uid));

        $res = $stmt->fetch();
        return $res;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function getUserByUserName($username)
    {
        $sql = "select * from users where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':username' => $username));
        $res = $stmt->fetch();
        return $res;
    }

    /**
     * @param $name
     * @param $userName
     * @param $ref
     * @return string
     * @throws \Exception
     */
    public function addNewUser($name, $userName, $ref)
    {
        $sql = "insert into users set name = :name, username = :username, ref = :ref";

        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute(
            array(
                ':name' => $name,
                ':username' => $userName,
                ':ref' => $ref
            )
        );
        if ($res === false) {
            $err = $stmt->errorInfo();
            throw new \Exception($err[2], $stmt->errorCode());
        }
        return $this->db->lastInsertId();
    }
}
