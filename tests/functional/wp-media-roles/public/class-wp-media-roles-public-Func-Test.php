<?php

/**
 * Sample test case.
 */
class Wp_Media_Roles_Public_Func_Test extends WP_UnitTestCase 
{
    public function setUp()
    {
//        parent::setUp();
        Mockery::close();
    }
    
    public static function tearDownAfterClass()
    {

    }
    
    public function tearDown()
    {
//        parent::tearDown();
        Mockery::close();
    }
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }
    
    /**
     * A single example test.
     */
    function test_sanityCheck() 
    {
        new Wp_Media_Roles_Public(
                "pluginname", 
                "version", 
                Mockery::mock('wpapi\v1\WordpressPluginApi')->makePartial(), 
                Mockery::mock('PhpApi')->makePartial(), 
                Mockery::mock('MembersApi')->makePartial());
        
        $this->assertTrue( true );
    }
}
