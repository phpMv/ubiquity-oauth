<?php
namespace Ubiquity\client\oauth;

use Hybridauth\Hybridauth;
use Hybridauth\Adapter\AdapterInterface;

/**
 * Ubiquity\client\oauth$OAuthManager
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class OAuthManager {

	private static $config;

	private static $oauth;

	private static $adapters;

	/**
	 * Creates Hybridauth object and load config
	 */
	public static function start(): void {
		self::$config = self::getConfig();
		self::$oauth = new Hybridauth(self::$config);
	}

	public static function getConfig(): array {
		$file = \ROOT . \DS . 'config' . \DS . 'oauth.php';
		if (file_exists($file)) {
			return self::$config = include $file;
		}
		return [];
	}

	/**
	 *
	 * @param string $name
	 * @return \Hybridauth\Adapter\AdapterInterface
	 */
	public static function authenticate($name): AdapterInterface {
		return self::$adapters[$name] = self::$oauth->authenticate($name);
	}

	/**
	 *
	 * @param string $name
	 * @return \Hybridauth\Adapter\AdapterInterface
	 */
	public static function getAdapter($name): AdapterInterface {
		if (! isset(self::$adapters[$name])) {
			return self::authenticate($name);
		}
		return self::$adapters[$name];
	}

	public static function disconnect($name): void {
		$adapter = self::getAdapter($name);
		$adapter->disconnect();
	}
}

