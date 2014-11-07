<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UsersTest extends \PHPUnit_Framework_TestCase {

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

        $this->assertTrue(is_numeric($ret['frawnf']['id']));
        $this->assertEquals('Frank Frawn', $ret['frawnf']['name']);
        $this->assertEquals('frawnf', $ret['frawnf']['username']);
        $this->assertEquals('?user=frawnf', $ret['frawnf']['ref']);
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

        $this->assertTrue(is_numeric($ret['homez']['id']));
        $this->assertEquals('Zelda Home', $ret['homez']['name']);
        $this->assertEquals('homez', $ret['homez']['username']);
        $this->assertEquals('?user=homez', $ret['homez']['ref']);

        $this->assertTrue(is_numeric($ret['sheldons']['id']));
        $this->assertEquals('Amy Sheldon', $ret['sheldons']['name']);
        $this->assertEquals('sheldons', $ret['sheldons']['username']);
        $this->assertEquals('?user=sheldons', $ret['sheldons']['ref']);
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
        $this->assertTrue(array_key_exists('balls', $ret));
        $this->assertEquals("Duplicate entry 'balls' for key 'idxUserName'", $ret['balls']);
    }
}
