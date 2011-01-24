<?php

require_once dirname(__FILE__).'/../../pluginRepository/AMFPHPAuthentication.php';
require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . "/../testData/services/AuthenticationService.php";

/**
 * Test class for AMFPHPAuthentication.
 * Generated by PHPUnit on 2011-01-17 at 15:13:05.
 */
class AMFPHPAuthenticationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AMFPHPAuthentication
     */
    protected $object;

    /**
     *
     * @var <AuthenticationService>
     */
    protected $serviceObj;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AMFPHPAuthentication;
        $this->serviceObj = new AuthenticationService();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        session_unset();
    }

    public function testAddRole()
    {
        AMFPHPAuthentication::addRole("admin");
        $roles = $_SESSION[AMFPHPAuthentication::SESSION_FIELD_ROLES];
        $this->assertEquals(array("admin"), $roles);
    }

    public function testClearSessionInfo()
    {
        AMFPHPAuthentication::addRole("bla");
        AMFPHPAuthentication::clearSessionInfo();
        $this->assertFalse(isset ($_SESSION[AMFPHPAuthentication::SESSION_FIELD_ROLES]));
    }


    public function testLoginAndAccess(){
        $this->serviceObj->login("admin", "adminPassword");
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");
    }

    public function testNormalAccessToUnprotectedMethods(){
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "logout");

    }

    /**
     * @expectedException Amfphp_Exception
     */
    public function testLogout(){
        $this->serviceObj->login("admin", "adminPassword");
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");
        $this->serviceObj->logout();
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");
    }
    /**
     * @expectedException Amfphp_Exception
     */
    public function testAccessWithoutAuthentication()
    {
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");
    }

    /**
     * @expectedException Amfphp_Exception
     */
    public function testBadRole(){
        $this->serviceObj->login("user", "userPassword");
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");

    }
    
    public function testRequestHeaderHandler()
    {
        $credentialsAssoc = array(Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID => "admin", Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD => "adminPassword");
        $credentialsHeader = new Amfphp_Core_Amf_Header(Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME, true, $credentialsAssoc);
        $this->object->requestHeaderHandler($credentialsHeader);
        $this->object->serviceObjectCreatedHandler($this->serviceObj, "adminMethod");
    }

    /**
     * @expectedException Amfphp_Exception
     */
    public function testWithHooksBlockAccess(){
        Amfphp_HookManager::getInstance()->callHooks(Amfphp_Core_Common_ServiceRouter::HOOK_SERVICE_OBJECT_CREATED, array($this->serviceObj, "adminMethod"));
    }

    public function testWithHooksGrantAccess(){
        $credentialsAssoc = array(Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID => "admin", Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD => "adminPassword");
        $credentialsHeader = new Amfphp_Core_Amf_Header(Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME, true, $credentialsAssoc);
        Amfphp_HookManager::getInstance()->callHooks(Amfphp_Gateway::HOOK_REQUEST_HEADER, array($credentialsHeader));
        Amfphp_HookManager::getInstance()->callHooks(Amfphp_Core_Common_ServiceRouter::HOOK_SERVICE_OBJECT_CREATED, array($this->serviceObj, "adminMethod"));
    }


}
?>
