<?php

require_once dirname(__FILE__) . '/../../pluginRepository/CharsetConverter.php';
require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';

/**
 * Test class for CharsetConverter.
 * Generated by PHPUnit on 2011-01-21 at 17:12:05.
 */
class CharsetConverterTest extends PHPUnit_Framework_TestCase {

    /**
     * @var CharsetConverter
     */
    protected $object;

    private $textInClientCharset;

    private $textInPhpCharset;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new CharsetConverter;
        $this->object->clientCharset = "UTF-8";
        $this->object->phpCharset = "ISO-8859-1";
        $this->object->method = CharsetConverter::METHOD_ICONV;
        $this->textInClientCharset = "éèê"; //utf-8
        $this->textInPhpCharset = iconv("UTF-8", "ISO-8859-1", $this->textInClientCharset);

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }
    
    public function testPacketRequestDeserializedHandler() {
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = new Amfphp_Core_Amf_Message(null, null, array($this->textInClientCharset));
        $ret = $this->object->packetRequestDeserializedHandler($testPacket);
        $modifiedPacket = $ret[0];
        $this->assertEquals($this->textInPhpCharset, $modifiedPacket->messages[0]->data[0]);
    }

    public function testPacketResponseDeserializedHandler() {
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = new Amfphp_Core_Amf_Message(null, null, array($this->textInPhpCharset));
        $ret = $this->object->packetResponseDeserializedHandler($testPacket);
        $modifiedPacket = $ret[0];
        $this->assertEquals($this->textInClientCharset, $modifiedPacket->messages[0]->data[0]);
    }

}

?>
