<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RolesTest extends \PHPUnit_Framework_TestCase {

    private $app;

    public function setUp()
    {
        parent::setUp();
        $this->app = new Application();
        $this->app['db'] =  new \PDO("mysql:host=127.0.0.1;dbname=simpleRoles", 'travis', '');
    }

    /**
     * @test
     */
    public function testListRoles()
    {
        $rolesController = new \SimpleRoles\Controller\Roles();
        $res = $rolesController->listRoles($this->app);

        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertEquals('read', $ret[0]['name']);
    }

    /**
     * @test
     */
    public function testGetUsersByRole()
    {
        $rolesController = new \SimpleRoles\Controller\Roles();
        $res = $rolesController->getUsers($this->app, 'admin');

        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 2);
        $this->assertEquals('balls', $ret[0]['username']);
    }

    /**
     * @test
     */
    public function testGetUsersByRoleForRoleWithNoUsers()
    {
        $rolesController = new \SimpleRoles\Controller\Roles();
        $res = $rolesController->getUsers($this->app, 'role_with_no_users');

        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 0);
    }

    /**
     * @test
     */
    public function testAddingNewRole()
    {
        $fields = array(
            'roles' => array (
                'name' => 'newRole1',
                'description' => 'Description of Controller CI/Unit Test Created Role'
            )
        );

        $data = json_encode($fields);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $rolesController = new \SimpleRoles\Controller\Roles();

        $res = $rolesController->newRoles($req, $this->app);
        $ret = json_decode($res->getContent(), true);

        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 1);

        $this->assertTrue(is_numeric($ret[0]['id']));
        $this->assertEquals('newRole1', $ret[0]['name']);
        $this->assertEquals(
            'Description of Controller CI/Unit Test Created Role',
            $ret[0]['description']
        );
    }

    /**
     * @test
     */
    public function testAddingNewRoleMultipleRoles()
    {
        $fields = array(
            'roles' => array (
                'name' => 'newRole10',
                'description' => 'Description of Controller CI/Unit Test Created Role'
            ), array(
                'name' => 'newRole11',
                'description' => 'Description of Controller CI/Unit Test Created Role'
            ), array(
                'name' => 'newRole12',
                'description' => 'Description of Controller CI/Unit Test Created Role'
            )
        );

        $data = json_encode($fields);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $rolesController = new \SimpleRoles\Controller\Roles();

        $res = $rolesController->newRoles($req, $this->app);
        $ret = json_decode($res->getContent(), true);

        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 3);

        $this->assertTrue(is_numeric($ret[0]['id']));
        $this->assertEquals('newRole10', $ret[0]['name']);
        $this->assertEquals(
            'Description of Controller CI/Unit Test Created Role',
            $ret[0]['description']
        );

        $this->assertEquals('newRole11', $ret[1]['name']);
    }

    /**
     * @test
     * @covers SimpleRoles\Controller\Roles::addUserToRole
     * @uses SimpleRoles\Model\Roles
     * @uses SimpleRoles\Model\Users
     */
    public function testAddingUsersToRole()
    {
        $role = "roleForUsersToBeAddedTest1";
        $groups = array(
            array (
                'role' => $role,
                'user' => 'poolea'
            ), array(
                'role' =>$role,
                'user' => 'leev'
            ), array(
                'role' => $role,
                'user' => 'kingr'
            ), array(
                'role' => $role,
                'user' => 'grays'
            ),
        );

        $data = json_encode($groups);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $rolesController = new \SimpleRoles\Controller\Roles();

        $res = $rolesController->addUserToRole($req, $this->app);
        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertTrue(array_key_exists('success', $ret));
        $this->assertEquals(4, count($ret['success']));
        $this->assertTrue(!array_key_exists('error', $ret));
    }

    /**
     * @test
     * @covers SimpleRoles\Controller\Roles::addUserToRole
     * @uses SimpleRoles\Model\Roles
     * @uses SimpleRoles\Model\Users
     */
    public function testAddingUsersToRoleAlreadyThere()
    {
        $role = "read";
        $user = "bakeri";
        $groups = array(
            array (
                'role' => $role,
                'user' => $user
            )
        );

        $data = json_encode($groups);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $rolesController = new \SimpleRoles\Controller\Roles();

        $res = $rolesController->addUserToRole($req, $this->app);
        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertTrue(!array_key_exists('success', $ret));
        $this->assertTrue(array_key_exists('error', $ret));
        $this->assertEquals(1, count($ret['error']));
    }

    /**
     * @test
     */
    public function testDeleteRole()
    {
        $role = "RoleToBeDeletedByControllerAutomatedTest";

        $rolesController = new \SimpleRoles\Controller\Roles();
        $res = $rolesController->listRoles($this->app, $role);
        $ret = json_decode($res->getContent(), true);

        $this->assertEquals($role, $ret[0]['name']);

        $res = $rolesController->deleteRole($this->app, $role);
        $this->assertEquals(200, $res->getStatusCode());

        $res = $rolesController->listRoles($this->app, $role);
        $ret = json_decode($res->getContent(), true);
        $this->assertEquals(0, count($ret));
    }

    /**
     * @test
     */
    public function testRemoveUsersFromRole()
    {
        $role = "RoleToBeSavedTestRemoveUsersController";

        $rolesController = new \SimpleRoles\Controller\Roles();

        $res = $rolesController->getUsers($this->app, $role);
        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertEquals(3, count($ret));

        $groups = array(
            array (
                'role' => $role,
                'user' => 'balls'
            )
        );
        $data = json_encode($groups);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $res = $rolesController->removeUser($req, $this->app);
        $this->assertEquals(200, $res->getStatusCode());
        $ret = json_decode($res->getContent(), true);
        $this->assertEquals(1, count($ret));

        $res = $rolesController->getUsers($this->app, $role);
        $ret = json_decode($res->getContent(), true);
        $this->assertTrue(is_array($ret));
        $this->assertEquals(2, count($ret));
    }
}
