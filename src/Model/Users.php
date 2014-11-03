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
}