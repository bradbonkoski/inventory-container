<?php
/**
 * Created by PhpStorm.
 * User: bradb
 * Date: 11/4/14
 * Time: 6:06 PM
 */

namespace SimpleRoles\Unit\Model;


use SimpleRoles\Model\Users;

class UsersTest extends \PHPUnit_Framework_TestCase {
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
    public function testGetUserInfoInvalid()
    {
        $userModel = new Users($this->db);
        $ret = $userModel->getUserInfo(-1);
        $this->assertTrue($ret === false);
    }

    /**
     * @test
     */
    public function testGetUserInfoValid()
    {
        $userModel = new Users($this->db);
        $ret = $userModel->getUserInfo(2);
        $this->assertTrue(is_array($ret));
        $this->assertEquals('balls', $ret['username']);
        $this->assertEquals('Sam Ball', $ret['name']);
        //print_r($ret);
    }
}
