<?php

namespace SimpleRoles\Unit\Model;
use SimpleRoles\Model\Roles;

/**
 * Created by PhpStorm.
 * User: bradb
 * Date: 11/2/14
 * Time: 8:21 PM
 */

class RolesTest extends \PHPUnit_Framework_TestCase {
    private $db;

    public function setUp()
    {
        $this->db = new \PDO("mysql:host=localhost;dbname=simpleRoles", 'test', '');
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
}
