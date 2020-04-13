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

	/**
	 *
	 * @var array
	 */
	private static $config;

	/**
	 *
	 * @var Hybridauth
	 */
	private static $oauth;

	/**
	 *
	 * @var \Hybridauth\Adapter\AdapterInterface[]
	 */
	private static $adapters;

	/**
	 * Creates Hybridauth object and load config
	 *
	 * @param ?string $callbackUrl
	 */
	public static function start(?string $callbackUrl = null): void {
		self::$config = self::getConfig();
		if (isset($callbackUrl)) {
			self::$config['callback'] = $callbackUrl;
		}
		self::$oauth = new Hybridauth(self::$config);
	}

	/**
	 *
	 * @param string $name
	 * @param string $callbackUrl
	 * @return \Hybridauth\Adapter\AdapterInterface
	 */
	public static function startAdapter(string $name, ?string $callbackUrl = null) {
		self::start($callbackUrl);
		return self::authenticate($name);
	}

	/**
	 *
	 * @return array
	 */
	public static function getConfig(): array {
		$file = \ROOT . \DS . 'config' . \DS . 'oauth.php';
		if (file_exists($file)) {
			return self::$config = include $file;
		}
		return [
			'callback' => '',
			'providers' => [],
			'debug_mode' => false,
			'debug_file' => './oauth.log'
		];
	}

	/**
	 *
	 * @param string $name
	 * @return \Hybridauth\Adapter\AdapterInterface
	 */
	public static function authenticate(string $name): AdapterInterface {
		return self::$adapters[$name] = self::$oauth->authenticate($name);
	}

	/**
	 *
	 * @param string $name
	 * @return \Hybridauth\Adapter\AdapterInterface
	 */
	public static function getAdapter(string $name): AdapterInterface {
		if (! isset(self::$adapters[$name])) {
			return self::authenticate($name);
		}
		return self::$adapters[$name];
	}

	/**
	 * Disconnect from $name provider and clears all access token
	 *
	 * @param string $name
	 *        	The provider name
	 */
	public static function disconnect(string $name): void {
		$adapter = self::getAdapter($name);
		$adapter->disconnect();
	}

	/**
	 *
	 * @param string $name
	 * @return array
	 */
	public static function getProviderConfig(string $name): array {
		return (self::$config['providers'][$name]) ?? ([
			'enabled' => false,
			'force' => false,
			'keys' => [
				'id' => '',
				'secret' => ''
			],
			'scope' => ''
		]);
	}
}

