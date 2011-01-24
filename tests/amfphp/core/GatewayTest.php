<?php

require_once dirname(__FILE__) . '/../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . "/../../testData/AMFTestData.php";
require_once dirname(__FILE__) . "/../../testData/TestServicesConfig.php";

/**
 * Test class for Amfphp_Gateway.
 * Generated by PHPUnit on 2010-12-09 at 11:56:28.
 */
class Amfphp_GatewayTest extends PHPUnit_Framework_TestCase {


    public function testService() {
        $amfTestData = new AMFTestData();
        $gateway = new Amfphp_Gateway($amfTestData->mirrorServiceRequestPacket);
        $testServiceConfig = new TestServicesConfig();
        $gateway->config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
        $gateway->config->serviceNames2Amfphp_Core_Common_ClassFindInfo = $testServiceConfig->serviceNames2Amfphp_Core_Common_ClassFindInfo;
        $ret = $gateway->service();
        $this->assertEquals(bin2hex($amfTestData->mirrorServiceResponsePacket), bin2hex($ret));
    }

    public function testEmptyMessage(){
        $gateway = new Amfphp_Gateway(null);
        $ret = $gateway->service();

        $this->assertFalse(strpos($ret, "onStatus") === false);

    }


}

?>
