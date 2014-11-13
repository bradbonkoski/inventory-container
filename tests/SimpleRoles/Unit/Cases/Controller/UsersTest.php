<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UsersTest extends \PHPUnit_Framework_TestCase {

    private $app;

    public function setUp()
    {
        parent::setUp();
        $this->app = new Application();
        $this->app['db'] =  new \PDO("mysql:host=127.0.0.1;dbname=simpleRoles", 'travis', '');
    }

    /**
     * @test
     * @covers SimpleRoles\Controller\Users::newUsers
     * @uses SimpleRoles\Model\Users
     */
    public function testAddingNewUser()
    {
        $fields = array(
            'users' => array(
                'name' => 'Frank Frawn',
                'username' => 'frawnf',
                'ref' => '?user=frawnf'
            )
        );

        $data = json_encode($fields);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $userController = new \SimpleRoles\Controller\Users();
        $res = $userController->newUsers($req, $this->app);
        $ret = json_decode($res->getContent(), true);

        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 1);

        $this->assertTrue(is_numeric($ret[0]['id']));
        $this->assertEquals('Frank Frawn', $ret[0]['name']);
        $this->assertEquals('frawnf', $ret[0]['username']);
        $this->assertEquals('?user=frawnf', $ret[0]['ref']);
    }

    /**
     * @test
     */
    public function testAddingNewUsers()
    {
        $fields = array(
            'users' => array (
                'name' => 'Zelda Home',
                'username' => 'homez',
                'ref' => '?user=homez'
            ), array(
                'name' => 'Amy Sheldon',
                'username' => 'sheldons',
                'ref' => '?user=sheldons'
            )
        );

        $data = json_encode($fields);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $userController = new \SimpleRoles\Controller\Users();
        $res = $userController->newUsers($req, $this->app);
        $ret = json_decode($res->getContent(), true);

        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 2);

        $this->assertTrue(is_numeric($ret[0]['id']));
        $this->assertEquals('Zelda Home', $ret[0]['name']);
        $this->assertEquals('homez', $ret[0]['username']);
        $this->assertEquals('?user=homez', $ret[0]['ref']);

        $this->assertTrue(is_numeric($ret[1]['id']));
        $this->assertEquals('Amy Sheldon', $ret[1]['name']);
        $this->assertEquals('sheldons', $ret[1]['username']);
        $this->assertEquals('?user=sheldons', $ret[1]['ref']);
    }

    /**
     * @test
     */
    public function testAddingNewUserThatIsADupe()
    {
        $fields = array(
            'users' => array(
                'name' => 'Same Ball',
                'username' => 'balls',
                'ref' => '?user=balls'
            )
        );

        $data = json_encode($fields);
        $req = new Request(array(), array(), array(), array(), array(), array(), $data);
        $userController = new \SimpleRoles\Controller\Users();
        $res = $userController->newUsers($req, $this->app);
        $ret = json_decode($res->getContent(), true);

        $this->assertTrue(is_array($ret));
        $this->assertTrue(count($ret) == 1);
        $this->assertEquals("Duplicate entry 'balls' for key 'idxUserName'", $ret[0]);
    }
}
