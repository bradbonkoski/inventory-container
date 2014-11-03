<?php
/**
 * Created by PhpStorm.
 * User: bradb
 * Date: 11/2/14
 * Time: 9:13 PM
 */

namespace SimpleRoles;


class DBTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function testCoreFunctionality()
    {
        $db = new DB();
        $dbh = $db->getConn();
        $this->assertTrue($dbh instanceof \PDO);
    }

}
