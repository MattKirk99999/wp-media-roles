<?php

/**
 * Sample test case.
 */
class Wp_Media_Roles_Public_Unit_Test extends WP_UnitTestCase 
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
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_doMediaRolePermissionsOpensMediaForPermitted() 
    {
        // configuration.
        
        $IS_PERMITTED_TO_VIEW = true;
        
        // mock dependencies.
        
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $phpApi = Mockery::mock('PhpApi');
        $membersApi =  Mockery::mock('MembersApi');
        $post_mock = Mockery::mock(new WP_Post((object) false));
                
        // init properties.
        
        $post_mock->ID = 10;
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock(
                'Wp_Media_Roles_Public[getMediaByMeta, getValidPathFromQueryArg, redirectToPdf, getOption, pluginDependenciesExist, hasPermissionToViewMedia]', 
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
        
        // setup dont-cares
        
        $mediaRolePublic->shouldReceive('getOption');
                
        // setup test.
        
        $mediaRolePublic->shouldReceive('hasPermissionToViewMedia')->once()->andReturn($IS_PERMITTED_TO_VIEW);
        $mediaRolePublic->shouldReceive('getValidPathFromQueryArg')->once();
        $mediaRolePublic->shouldReceive('getMediaByMeta')->once()->andReturn($post_mock);
        $mediaRolePublic->shouldReceive('redirectToPdf')->once();
        $phpApi->shouldReceive('___exit')->once();
        
        // run test.
        
        $mediaRolePublic->doMediaRolePermissions($wordpressApi, $phpApi, $membersApi);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_doMediaRolePermissionsDeniesMediaForUnpermitted() 
    {
        // configuration.

        $IS_PERMITTED_TO_VIEW = false;
        
        // mock dependencies.
        
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $phpApi = Mockery::mock('PhpApi');
        $membersApi =  Mockery::mock('MembersApi');
        $post_mock = Mockery::mock(new WP_Post((object) false));
                
        // init properties.
        
        $post_mock->ID = 10;
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock(
                'Wp_Media_Roles_Public[getMediaByMeta, getValidPathFromQueryArg, redirectToPdf, getOption, pluginDependenciesExist, hasPermissionToViewMedia]', 
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
        
        // setup dont-cares
        
        $mediaRolePublic->shouldReceive('getOption');
        
        // setup test.
        
        $mediaRolePublic->shouldReceive('hasPermissionToViewMedia')->once()->andReturn($IS_PERMITTED_TO_VIEW);
        $mediaRolePublic->shouldReceive('getValidPathFromQueryArg')->once();
        $mediaRolePublic->shouldReceive('getMediaByMeta')->once()->andReturn($post_mock);
        $wordpressApi->shouldReceive('wp_redirect')->once();
        $phpApi->shouldReceive('___exit')->once();
        
        $mediaRolePublic->shouldReceive('redirectToPdf')->never();
        
        // run test.
        
        $mediaRolePublic->doMediaRolePermissions($wordpressApi, $phpApi, $membersApi);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_getValidPathFromQueryArgValidatesAndReturnsValidPath() 
    {
        // configuration.
        
        $INPUT_GET = "get_file";
        $URL = "/wp-content/2017/03/file.pdf";
        
        // mock dependencies.

        $phpApi = Mockery::mock('PhpApi');
     
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public')->makePartial();
        
        // setup dont-cares
        
        $phpApi->shouldReceive('filter_input')->andReturn($URL);
        
        // setup test.
        
        $phpApi->shouldReceive('file_exists')->once()->andReturn(true);
        
        // run test.
        
        $return = $mediaRolePublic->getValidPathFromQueryArg( $phpApi, $INPUT_GET);
        
        $this->assertEquals($URL, $return);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * @expectedException Exception
     */
    function test_getValidPathFromQueryArgRejectsInvalidPath() 
    {
        // configuration.
        
        $INPUT_GET = "get_file";
        $URL = "/wp-content/2017/03/file.php";
        
        // mock dependencies.

        $phpApi = Mockery::mock('PhpApi');
     
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public')->makePartial();
        
        // setup test.
        
        $phpApi->shouldReceive('filter_input')->once()->andReturn($URL);
        
        // run test.
        
        $mediaRolePublic->getValidPathFromQueryArg( $phpApi, $INPUT_GET);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_hasPermissionToViewMediaReturnsTrueWhenHasPermission() 
    {
        // configuration.
        
        $FAIL_SECURE = true;
        $IS_ADMIN = false;
        $PLUGIN_DEPENDENCIES_EXIST = true;
        $CAN_VIEW_BY_ROLE = true;
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $post_mock = Mockery::mock(new WP_Post((object) false));
        $phpApi = Mockery::mock('PhpApi');
        
        // init properties.
        
        $post_mock->ID = 10;
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public[pluginDependenciesExist, getOption]',
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
                
        // setup test.
        
        $mediaRolePublic->shouldReceive('getOption')->andReturn($FAIL_SECURE);
        $wordpressApi->shouldReceive('is_admin')->andReturn($IS_ADMIN);
        $mediaRolePublic->shouldReceive('pluginDependenciesExist')->andReturn($PLUGIN_DEPENDENCIES_EXIST);
        $membersApi->shouldReceive('members_can_current_user_view_post')->once()->andReturn($CAN_VIEW_BY_ROLE);
        
        // run test.
        
        $return = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        
        $this->assertEquals(true, $return);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_hasPermissionToViewMediaReturnsTrueForAdmin() 
    {
        // configuration.
        
        $FAIL_SECURE = true;
        $IS_ADMIN = true;
        $PLUGIN_DEPENDENCIES_EXIST = false;
        $CAN_VIEW_BY_ROLE = false;
        $phpApi = Mockery::mock('PhpApi');
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $post_mock = Mockery::mock(new WP_Post((object) false));
        
        // init properties.
        
        $post_mock->ID = 10;
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public[pluginDependenciesExist, getOption]',
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
                
        // setup test.
        
        $mediaRolePublic->shouldReceive('getOption')->andReturn($FAIL_SECURE);
        $wordpressApi->shouldReceive('is_admin')->once()->andReturn($IS_ADMIN);
        $mediaRolePublic->shouldReceive('pluginDependenciesExist')->andReturn($PLUGIN_DEPENDENCIES_EXIST);
        $membersApi->shouldReceive('members_can_current_user_view_post')->andReturn($CAN_VIEW_BY_ROLE);
        
        // run test.
        
        $return = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        
        $this->assertEquals(true, $return);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_hasPermissionToViewMediaFailsSecurelyWhenDependenciesNotMet() 
    {
        // configuration.
        
        $FAIL_SECURE = array(true, false);
        $IS_ADMIN = false;
        $PLUGIN_DEPENDENCIES_EXIST = false;
        $CAN_VIEW_BY_ROLE = true;
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $post_mock = Mockery::mock(new WP_Post((object) false));
        $phpApi = Mockery::mock('PhpApi');
        
        // init properties.
        
        $post_mock->ID = 10;
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public[pluginDependenciesExist, getOption]',
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
                
        // setup test.
        
        $mediaRolePublic->shouldReceive('getOption')->andReturn($FAIL_SECURE[0], $FAIL_SECURE[1]);
        $wordpressApi->shouldReceive('is_admin')->andReturn($IS_ADMIN);
        $mediaRolePublic->shouldReceive('pluginDependenciesExist')->twice()->andReturn($PLUGIN_DEPENDENCIES_EXIST);
        $membersApi->shouldReceive('members_can_current_user_view_post')->andReturn($CAN_VIEW_BY_ROLE);
        
        // run test.
        
        $returnFailSecure = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        $returnFailOpen = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        
        $this->assertEquals(false, $returnFailSecure);
        
        $this->assertEquals(true, $returnFailOpen);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_hasPermissionToViewMediaFailsSecurelyWhenPostIdIsNull() 
    {
        // configuration.
        
        $FAIL_SECURE = array(true, false);
        $IS_ADMIN = false;
        $PLUGIN_DEPENDENCIES_EXIST = true;
        $CAN_VIEW_BY_ROLE = true;
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        $wordpressApi = Mockery::mock('wpapi\v1\WordpressPluginApi');
        $post_mock = null;
        $phpApi = Mockery::mock('PhpApi');
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public[pluginDependenciesExist, getOption]',
                array ("pluginname", "version", $wordpressApi, $phpApi, $membersApi));
                
        // setup test.
        
        $mediaRolePublic->shouldReceive('getOption')->andReturn($FAIL_SECURE[0], $FAIL_SECURE[1]);
        $wordpressApi->shouldReceive('is_admin')->andReturn($IS_ADMIN);
        $mediaRolePublic->shouldReceive('pluginDependenciesExist')->andReturn($PLUGIN_DEPENDENCIES_EXIST);
        $membersApi->shouldReceive('members_can_current_user_view_post')->andReturn($CAN_VIEW_BY_ROLE);
        
        // run test.
        
        $returnFailSecure = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        $returnFailOpen = $mediaRolePublic->hasPermissionToViewMedia( $wordpressApi, $membersApi, $post_mock);
        
        $this->assertEquals(false, $returnFailSecure);
        
        $this->assertEquals(true, $returnFailOpen);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_pluginDependenciesExistReturnsTrueWhenDependenciesExist() 
    {
        // configuration.
        
        $MEMBERS_IS_ACTIVE = true;
        $MEMBERS_IS_SYNCED = true;
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public')->makePartial();
                
        // setup test.
        
        $membersApi->shouldReceive('is_active')->andReturn($MEMBERS_IS_ACTIVE);
        $membersApi->shouldReceive('is_synced')->andReturn($MEMBERS_IS_SYNCED);
        
        // run test.
        
        $return = $mediaRolePublic->pluginDependenciesExist( $membersApi );
        
        $this->assertEquals(true, $return);
    }
    
    /**
     * Groups:
     * 
     * @group public
     * 
     * Expectations:
     * 
     * none
     */
    function test_pluginDependenciesExistReturnsFalseWhenMembersIsNotActive() 
    {
        // configuration.
        
        $MEMBERS_IS_ACTIVE = false;
        $MEMBERS_IS_SYNCED = true;
        
        // mock dependencies.

        $membersApi =  Mockery::mock('MembersApi');
        
        // mock tested class.
        
        $mediaRolePublic = Mockery::mock('Wp_Media_Roles_Public')->makePartial();
                
        // setup test.
        
        $membersApi->shouldReceive('is_active')->andReturn($MEMBERS_IS_ACTIVE);
        $membersApi->shouldReceive('is_synced')->andReturn($MEMBERS_IS_SYNCED);
        
        // run test.
        
        $return = $mediaRolePublic->pluginDependenciesExist( $membersApi );
        
        $this->assertEquals(false, $return);
    }
}
