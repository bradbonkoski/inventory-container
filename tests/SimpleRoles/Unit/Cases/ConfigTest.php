<?php

namespace SimpleRoles;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function testGetConfigByValue()
    {
        $ret = Config::getConfigValue('db', 'port');
        $this->assertEquals(3306, $ret);

        apc_delete(Config::KEYROOT);
        try {
            $ret = Config::getConfigValue('notThere', 'nosuchkey');
        } catch (\Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('missed expected exception');
    }

    /**
     * @test
     */
    public function testGetSection()
    {
        $ret = config::getConfigSection('db');
        $this->assertTrue(is_array($ret));
        $this->assertEquals('simpleRoles', $ret['dbname']);

        apc_delete(config::KEYROOT);
        try {
            $ret = config::getConfigSection('notThere');
        } catch (\Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('missed expected exception');
    }
}
