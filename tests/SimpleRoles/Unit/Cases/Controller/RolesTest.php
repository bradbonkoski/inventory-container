<?php

use Silex\Application;

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
}
