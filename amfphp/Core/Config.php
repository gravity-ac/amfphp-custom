<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * responsable for loading and maintaining Amfphp configuration
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Config {

    /**
     * paths to folders containing services(relative or absolute)
     * @var <array> of paths
     */
    public $serviceFolders;

    /**
     * a dictionary of service classes represented in a ClassFindInfo.
     * The key is the name of the service, the value is the class find info.
     * for example: $serviceNames2ClassFindInfo["AmfphpDiscoveryService"] = new Amfphp_Core_Common_ClassFindInfo( dirname(__FILE__) . '/AmfphpDiscoveryService.php', 'AmfphpDiscoveryService');
     * The forward slash is important, don't use "\'!     
     * @var <array> of ClassFindInfo
     */
    public $serviceNames2ClassFindInfo;

    /**
     * set to true if you want the service router to check if the number of arguments received by amfPHP matches with the method being called.
     * This should be set to false in production for performance reasons
     * default is true
     * @var Boolean
     */
    public $checkArgumentCount = true;

    /**
     * paths to the folder containing the plugins. defaults to AMFPHP_ROOTPATH . '/Plugins/'
     * @var array
     */
    public $pluginsFolders;

    /**
     * array containing untyped plugin configuration data. Add as needed. The advised format is the name of the plugin as key, and then
     * paramName/paramValue pairs as an array.
     * example: array('plugin' => array( 'paramName' =>'paramValue'))
     * The array( 'paramName' =>'paramValue') will be passed as is to the plugin at construction time.
     * 
     * @var array
     */
    public $pluginsConfig;

    /**
     * array containing configuration data that is shared between the plugins. The format is paramName/paramValue pairs as an array.
     * 
     * @var array
     */
    public $sharedConfig;

    /**
     * if true, there will be detailed information in the error messages, including confidential information like paths.
     * So it is advised to set to true for development purposes and to false in production.
     * default is true.
     * Set in the shared config.
     * for example
     * $this->sharedConfig[self::CONFIG_RETURN_ERROR_DETAILS] = true;
     * @var Boolean
     */

    const CONFIG_RETURN_ERROR_DETAILS = 'returnErrorDetails';

    /** Maximum accepted request body size in bytes. */
    const CONFIG_MAX_REQUEST_BYTES = 'maxRequestBytes';

    /** Reject AMF envelopes older than version 3 when enabled. */
    const CONFIG_REQUIRE_AMF3_ENVELOPE = 'requireAmf3Envelope';

    /**
     * array of plugins that are available but should be disabled
     * @var array
     */
    public $disabledPlugins;

    /**
     * constructor
     */
    public function __construct() {
        $this->serviceFolders = array();
        $this->serviceFolders [] = dirname(__FILE__) . '/../Services/';
        $this->serviceNames2ClassFindInfo = array();
        $this->pluginsFolders = array(AMFPHP_ROOTPATH . 'Plugins/');
        $this->pluginsConfig = array();
        $this->sharedConfig = array();
        $this->sharedConfig[self::CONFIG_MAX_REQUEST_BYTES] = 8 * 1024 * 1024;
        $this->sharedConfig[self::CONFIG_REQUIRE_AMF3_ENVELOPE] = false;
        $this->disabledPlugins = array();
        
        // Secure defaults: the core AMF gateway needs no optional plugins.
        // ErrorHandler remains enabled so exceptions are returned as AMF faults.
        $this->disabledPlugins[] = 'AmfphpAuthentication';
        $this->disabledPlugins[] = 'AmfphpCharsetConverter';
        $this->disabledPlugins[] = 'AmfphpDiscovery';
        $this->disabledPlugins[] = 'AmfphpDummy';
        $this->disabledPlugins[] = 'AmfphpFlexMessaging';
        $this->disabledPlugins[] = 'AmfphpGet';
        $this->disabledPlugins[] = 'AmfphpJson';
        $this->disabledPlugins[] = 'AmfphpLogger';
        $this->disabledPlugins[] = 'AmfphpMonitor';
        $this->disabledPlugins[] = 'AmfphpVoConverter';
        
        //some useful examples of setting a config value in a plugin:
        //$this->pluginsConfig['AmfphpDiscovery']['restrictAccess'] = false;
        //$this->pluginsConfig['AmfphpVoConverter']['enforceConversion'] = true;
    }

}

?>
