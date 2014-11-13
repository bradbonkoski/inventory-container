<?php
namespace SimpleRoles\Model;

/**
 * Class Roles
 * @package SimpleRoles\Model
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 */
class Users
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getUserInfo($uid)
    {
        $sql = "select * from users where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id' => $uid));

        $res = $stmt->fetch();
        return $res;
    }

    public function getUserByUserName($username)
    {
        $sql = "select * from users where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':username' => $username));
        $res = $stmt->fetch();
        return $res;
    }

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
