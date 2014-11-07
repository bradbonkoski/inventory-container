<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RolesTest extends \PHPUnit_Framework_TestCase {

    private $app;

    public function setUp()
    {
        parent::setUp();
        $this->app = new Application();
        $this->app['db'] =  new \PDO("mysql:host=localhost;dbname=simpleRoles", 'test', '');
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
                'desc' => 'Description of Controller CI/Unit Test Created Role'
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
            $ret[0]['desc']
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
                'desc' => 'Description of Controller CI/Unit Test Created Role'
            ), array(
                'name' => 'newRole11',
                'desc' => 'Description of Controller CI/Unit Test Created Role'
            ), array(
                'name' => 'newRole12',
                'desc' => 'Description of Controller CI/Unit Test Created Role'
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
            $ret[0]['desc']
        );

        $this->assertEquals('newRole11', $ret[1]['name']);
    }
}
