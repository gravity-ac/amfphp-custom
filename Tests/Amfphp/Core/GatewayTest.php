<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core
 */
/**
 *  includes
 *  */
require_once dirname(__FILE__) . '/../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../TestData/AmfTestData.php';
require_once dirname(__FILE__) . '/../../TestData/TestServicesConfig.php';

/**
 * Test class for Amfphp_Core_Gateway.
 * @package Tests_Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_GatewayTest extends \PHPUnit\Framework\TestCase {
    private const AIR_AMF3_STRING_REQUEST = '00030000000100124d757365446174612f676574537472696e6700022f31000000120a0000000102000a414d46332070726f6265';

    /**
     * test service
     */
    public function testService() {
        $amfTestData = new AmfTestData();
        $testServiceConfig = new TestServicesConfig();
        $testServiceConfig->serviceFolders = $testServiceConfig->serviceFolders;
        $testServiceConfig->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
        $gateway = new Amfphp_Core_Gateway(array(), array(), $amfTestData->testServiceRequestPacket, Amfphp_Core_Amf_Constants::CONTENT_TYPE, $testServiceConfig);
        $ret = $gateway->service();
        restore_error_handler();
        $this->assertEquals(bin2hex($amfTestData->testServiceResponsePacket), bin2hex($ret));
    }

    public function testAmf3EnvelopePolicyAcceptsCapturedAirFraming() {
        $handler = new Amfphp_Core_Amf_Handler(array(
            Amfphp_Core_Config::CONFIG_REQUIRE_AMF3_ENVELOPE => true,
        ));

        $packet = $handler->deserialize(array(), array(), hex2bin(self::AIR_AMF3_STRING_REQUEST));

        $this->assertSame(Amfphp_Core_Amf_Constants::AMF3_ENCODING, $packet->amfVersion);
        $this->assertSame(array('AMF3 probe'), $packet->messages[0]->data);
    }

    public function testAmf3EnvelopePolicyRejectsVersionZeroEnvelope() {
        $handler = new Amfphp_Core_Amf_Handler(array(
            Amfphp_Core_Config::CONFIG_REQUIRE_AMF3_ENVELOPE => true,
        ));
        $request = hex2bin(self::AIR_AMF3_STRING_REQUEST);
        $request[1] = "\x00";

        $this->expectException(Amfphp_Core_Exception::class);
        $this->expectExceptionMessage('AMF version 3 envelope required');
        $handler->deserialize(array(), array(), $request);
    }

    public function testRejectsTruncatedPacketsCleanly() {
        $handler = new Amfphp_Core_Amf_Handler(array());

        $this->expectException(Amfphp_Core_Exception::class);
        $this->expectExceptionMessage('Truncated or malformed AMF packet');
        $handler->deserialize(array(), array(), "\x00\x03\x00");
    }

    public function testRejectsOversizedRequestsBeforeParsing() {
        $handler = new Amfphp_Core_Amf_Handler(array(
            Amfphp_Core_Config::CONFIG_MAX_REQUEST_BYTES => 4,
        ));

        $this->expectException(Amfphp_Core_Exception::class);
        $this->expectExceptionMessage('size limit');
        $handler->deserialize(array(), array(), str_repeat("\x00", 5));
    }


}

?>
