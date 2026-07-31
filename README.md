# AMFPHP Custom

A maintained AMF0/AMF3 remoting library for PHP 8.4 and later, based on AMFPHP 2.2.2.

## Install

```bash
composer require gravity-ac/amfphp-custom
```

Composer loads `amfphp/ClassLoader.php`, which provides the library's legacy class-loader compatibility.

## Create a gateway

```php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$config = new Amfphp_Core_Config();
$config->serviceFolders = array(dirname(__DIR__) . '/services/');
$config->sharedConfig[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS] = false;
$config->sharedConfig[Amfphp_Core_Config::CONFIG_MAX_REQUEST_BYTES] = 1024 * 1024;
$config->sharedConfig[Amfphp_Core_Config::CONFIG_REQUIRE_AMF3_ENVELOPE] = true;

$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);
$gateway->service();
$gateway->output();
```

Keep the gateway and service classes outside the public document root where possible. Authenticate and authorize calls before dispatch, expose only intended public service methods, and use HTTPS. All optional plugins except `AmfphpErrorHandler` are disabled by default; enable only a plugin your application deliberately requires. Detailed error responses must be disabled in production.

The default request-body limit is 8 MiB and can be lowered with `CONFIG_MAX_REQUEST_BYTES`. Services beginning with path traversal syntax and methods that are magic, underscored, malformed, or non-public are rejected. `CONFIG_REQUIRE_AMF3_ENVELOPE` is opt-in so existing AMF0 consumers are not broken by a package update.

## AMF3 interoperability

AIR `NetConnection` clients should explicitly set `objectEncoding = ObjectEncoding.AMF3` before `connect()`. Some clients place AMF3 values inside the standard AMF envelope, so do not remove AMF0 envelope support based only on the envelope version. Verify the first body marker on the wire before enabling an AMF3-only policy.

## Development

```bash
composer install
composer check
composer audit
```

`composer check` runs syntax checks, PHPUnit, PHPStan, and PHP 8.4 compatibility checks. CI runs the suite on PHP 8.4 and 8.5.

## License

BSD 3-Clause. See `license.txt`.
