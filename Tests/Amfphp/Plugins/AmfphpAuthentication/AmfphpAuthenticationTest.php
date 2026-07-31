<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_Authentication
 */
/**
 *  includes
 *  */
require_once dirname(__FILE__) . '/../../../../amfphp/Plugins/AmfphpAuthentication/AmfphpAuthentication.php';
require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/Services/TestAuthenticationService.php';

/**
 * Test class for AmfphpAuthentication.
 * @package Tests_Amfphp_Plugins_Authentication
 * @author Ariel Sommeria-klein
 */
class AmfphpAuthenticationTest extends \PHPUnit\Framework\TestCase {

    /**
     * object
     * @var AmfphpAuthentication
     */
    protected $object;

    /**
     * service obj
     * @var AuthenticationService
     */
    protected $serviceObj;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void {
        $this->object = new AmfphpAuthentication;
        $this->serviceObj = new TestAuthenticationService();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void {
        session_unset();
    }

    /**
     * test add role
     */
    public function testAddRole() {
        AmfphpAuthentication::addRole('admin');
        $roles = $_SESSION[AmfphpAuthentication::SESSION_FIELD_ROLES];
        $this->assertEquals(array('admin' => true), $roles);
    }

    /**
     * test clear session info
     */
    public function testClearSessionInfo() {
        AmfphpAuthentication::addRole('bla');
        AmfphpAuthentication::clearSessionInfo();
        $this->assertFalse(isset($_SESSION[AmfphpAuthentication::SESSION_FIELD_ROLES]));
    }

    /**
     * test login and access
     */
    public function testLoginAndAccess() {
        $this->serviceObj->login('admin', 'adminPassword');
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
        $this->addToAssertionCount(1);
    }

    /**
     * test normal access to unprotected methods
     */
    public function testNormalAccessToUnprotectedMethods() {
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'logout');
        $this->addToAssertionCount(1);
    }

    /**
     * test logout
     * @expectedException Amfphp_Core_Exception
     */
    public function testLogout() {
        $this->serviceObj->login('admin', 'adminPassword');
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
        $this->serviceObj->logout();
        $this->expectException(Amfphp_Core_Exception::class);
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
    }

    /**
     * test access without authentication
     * @expectedException Amfphp_Core_Exception
     */
    public function testAccessWithoutAuthentication() {
        $this->expectException(Amfphp_Core_Exception::class);
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
    }

    /**
     * test bad role
     * @expectedException Amfphp_Core_Exception
     */
    public function testBadRole() {
        $this->serviceObj->login('user', 'userPassword');
        $this->expectException(Amfphp_Core_Exception::class);
        $this->object->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
    }

    /**
     * test get amf request header handler filter
     */
    public function testGetAmfRequestHeaderHandlerFilter() {
        $credentialsAssoc = new stdClass();
        $userIdField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID;
        $passwordField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD;
        $credentialsAssoc->$userIdField = 'admin';
        $credentialsAssoc->$passwordField = 'adminPassword';
        $credentialsHeader = new Amfphp_Core_Amf_Header(Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME, true, $credentialsAssoc);
        $ret = $this->object->filterAmfRequestHeaderHandler(null, $credentialsHeader);
        $this->assertEquals($this->object, $ret);

        $otherHeader = new Amfphp_Core_Amf_Header('bla');
        $ret = $this->object->filterAmfRequestHeaderHandler(null, $otherHeader);
        $this->assertEquals(null, $ret);
    }

    /**
     * test with filters block access
     * @expectedException Amfphp_Core_Exception
     */
    public function testWithFiltersBlockAccess() {
        $this->expectException(Amfphp_Core_Exception::class);
        Amfphp_Core_FilterManager::getInstance()->callFilters(Amfphp_Core_Common_ServiceRouter::FILTER_SERVICE_OBJECT, $this->serviceObj, 'TestService', 'adminMethod');
    }

    /**
     * test with filters grant access
     */
    public function testWithFiltersGrantAccess() {
        $credentialsAssoc = new stdClass();
        $userIdField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_USERID;
        $passwordField = Amfphp_Core_Amf_Constants::CREDENTIALS_FIELD_PASSWORD;
        $credentialsAssoc->$userIdField = 'admin';
        $credentialsAssoc->$passwordField = 'adminPassword';
        $credentialsHeader = new Amfphp_Core_Amf_Header(Amfphp_Core_Amf_Constants::CREDENTIALS_HEADER_NAME, true, $credentialsAssoc);
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $ret = $filterManager->callFilters(Amfphp_Core_Amf_Handler::FILTER_AMF_REQUEST_HEADER_HANDLER, null, $credentialsHeader);
        $ret->handleRequestHeader($credentialsHeader);
        $ret->filterServiceObject($this->serviceObj, 'AnyService', 'adminMethod');
        $this->addToAssertionCount(1);
    }

}

?>
