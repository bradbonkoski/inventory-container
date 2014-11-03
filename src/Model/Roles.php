<?php
namespace SimpleRoles\Model;

/**
 * Class Roles
 * @package SimpleRoles\Model
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 */
class Roles
{

    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function listRoles($search = null)
    {
        $sql = "select * from role";
        if (strlen($search) > 0) {
            $sql .= " where name like '%$search%'";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $res = $stmt->fetchAll();
        return $res;
    }

    public function getRoleInfo($role)
    {
        $sql = "select * from role where ";
        if (is_numeric($role)) {
            $sql .= "id = :role";
        } else {
            $sql .= "name = :role";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':role' => $role));
        $res = $stmt->fetch();
        return $res;
    }

    public function getUserIdsForRole($role)
    {
        $roleInfo = $this->getRoleInfo($role);
        $sql = "select user_id from user_roles where role_id = :rid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':rid' => $roleInfo['id']));
        $res = $stmt->fetchAll();

        return $res;
    }

}