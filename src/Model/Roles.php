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

    /**
     * @param $role
     * @return bool
     */
    public function roleExists($role)
    {
        $sql = "select id from role where name = :role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':role' => $role));
        $res = $stmt->fetch();
        if ($res === null || $res['id'] <= 0) {
            return false;
        }
        return $res['id'];
    }

    /**
     * @param null $search
     * @return array
     */
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

    /**
     * @param $role
     * @return mixed
     */
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

    /**
     * @param $role
     * @return array
     */
    public function getUserIdsForRole($role)
    {
        $roleInfo = $this->getRoleInfo($role);
        $sql = "select user_id from user_roles where role_id = :rid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':rid' => $roleInfo['id']));
        $res = $stmt->fetchAll();

        return $res;
    }

    /**
     * @param $rid
     * @param $uid
     * @throws \Exception
     */
    public function addUserToRole($rid, $uid)
    {
        $sql = "insert into user_roles set user_id = :uid, role_id = :rid";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute(array(':uid' => $uid, ':rid' => $rid));
        if ($res === false) {
            $err = $stmt->errorInfo();
            throw new \Exception($err[2], $stmt->errorCode());
        }
    }

    /**
     * @param $role
     * @param $desc
     * @return string
     */
    public function createNewRole($role, $desc)
    {
        $sql = "insert into role set name = :role, description = :desc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':role' => $role, ':desc' => $desc));
        return $this->db->lastInsertId();
    }

    /**
     * @param $role
     * @return bool
     */
    public function deleteRole($role)
    {
        $sql = "delete from role where name = :role";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute(array(':role' => $role));
        return $res;
    }

    /**
     * @param $rid
     * @param $uid
     * @return bool
     */
    public function removeUserFromRole($rid, $uid)
    {
        $sql = "delete from user_roles where user_id = :uid and role_id = :rid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array(':uid' => $uid, ':rid' => $rid));
    }
}
