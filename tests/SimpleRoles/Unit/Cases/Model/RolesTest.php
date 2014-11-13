<?php

namespace SimpleRoles\Unit\Model;
use SimpleRoles\Model\Roles;


class RolesTest extends \PHPUnit_Framework_TestCase {
    private $db;

    public function setUp()
    {
        $this->db = new \PDO("mysql:host=127.0.0.1;dbname=simpleRoles", 'travis', '');
        parent::setUp();
    }

    public function tearDown()
    {
        $this->db = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function testListAll()
    {
        $roleModel = new Roles($this->db);
        $data = $roleModel->listRoles();
        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data)>0);
    }

    /**
     * @test
     */
    public function testListRolesSearch()
    {
        $roleModel = new Roles($this->db);
        $ret = $roleModel->listRoles('some_custom_test_role');
        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) > 0);
        $this->assertTrue(count($ret) == 1);
        $this->assertEquals($ret[0]['id'], 5);
    }

    /**
     * @test
     */
    public function testGetRoleInfoWithId()
    {
        $roleModel = new Roles($this->db);
        $ret = $roleModel->getRoleInfo(1);

        $this->assertEquals($ret['id'], 1);
        $this->assertEquals($ret['name'], 'read');
    }

    /**
     * @test
     */
    public function testGetRoleInfoWithRoleName()
    {
        $roleModel = new Roles($this->db);
        $ret = $roleModel->getRoleInfo('read');

        $this->assertEquals($ret['id'], 1);
        $this->assertEquals($ret['name'], 'read');
    }

    /**
     * @test
     */
    public function testRoleExists()
    {
        $roleModel = new Roles($this->db);
        $ret = $roleModel->roleExists('read');
        $this->assertEquals(1, $ret);

        // This role should not exist!
        $ret = $roleModel->roleExists('someomfkjsgskljgsdvfs');
        $this->assertTrue($ret === false);
    }

    /**
     * @test
     */
    public function testAddingNewRole()
    {
        $roleModel = new Roles($this->db);
        $ret = $roleModel->createNewRole('newTestRole', 'testing creation of new role');
        $this->assertTrue(is_numeric($ret));
        $this->assertTrue($ret > 0);
    }

    /**
     * @test
     * @covers SimpleRoles\Model\Roles::addUserToRole
     * @uses SimpleRoles\Model\Roles
     */
    public function testAddUserToRole()
    {
        //role: ModelAddUserToRoleTest
        $rid = 8;
        $roleModel = new Roles($this->db);
        try {
            $roleModel->addUserToRole($rid, 900);
        } catch (\Exception $e) {
            $this->fail("Unexpected Exception");
            return;
        }
        $this->assertTrue(true);

        try {
            $roleModel->addUserToRole($rid, 900);
        } catch (\Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail("Missed Expected Exception");
    }

    /**
     * @test
     * @covers SimpleRoles\Model\Roles::deleteRole
     * @uses SimpleRoles\Model\Roles
     */
    public function testDeleteRole()
    {
        $role = "RoleToBeDeletedByAutomatedTest";
        $roleModel = new Roles($this->db);
        $ret = $roleModel->getRoleInfo($role);
        $this->assertEquals($role, $ret['name']);

        $roleModel->deleteRole($role);

        $ret = $roleModel->getRoleInfo($role);
        $this->assertTrue($ret == null);
    }

    /**
     * @test
     * @covers SimpleRoles\Model\Roles::removeUserFromRole
     * @uses SimpleRoles\Model\Roles
     */
    public function testRemoveUserFromRole()
    {
        $role = "RoleToBeSavedTestRemoveUsersModel";
        $roleModel = new Roles($this->db);
        $ret = $roleModel->getUserIdsForRole(12);
        $this->assertEquals(2, count($ret));

        $ret = $roleModel->removeUserFromRole(12, 2);

        $ret = $roleModel->getUserIdsForRole(12);
        $this->assertEquals(1, count($ret));
    }
}
