<?php
namespace Ubiquity\client\oauth;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;

class OAuthAdmin {

	private static $config;

	public const CONFIG_FILE_NAME = 'oauth.php';

	public const PROVIDERS = [
		'Amazon' => [
			'type' => 'OAuth2',
			'dev' => 'https://developer.amazon.com'
		],
		'AOLOpenID' => [
			'type' => 'OpenID'
		],
		'Authentiq' => [
			'type' => 'OAuth2'
		],
		'BitBucket' => [
			'type' => 'OAuth2'
		],
		'Blizzard' => [
			'type' => 'OAuth2'
		],
		'Discord' => [
			'type' => 'OAuth2'
		],
		'Disqus' => [
			'type' => 'OAuth2'
		],
		'Dribbble' => [
			'type' => 'OAuth2'
		],
		'Facebook' => [
			'type' => 'OAuth2'
		],
		'Foursquare' => [
			'type' => 'OAuth2'
		],
		'GitHub' => [
			'type' => 'OAuth2'
		],
		'GitLab' => [
			'type' => 'OAuth2'
		],
		'Google' => [
			'type' => 'OAuth2',
			'dev' => 'https://console.developers.google.com/'
		],
		'Instagram' => [
			'type' => 'OAuth2'
		],
		'LinkedIn' => [
			'type' => 'OAuth2'
		],
		'Mailru' => [
			'type' => 'OAuth2'
		],
		'MicrosoftGraph' => [
			'type' => 'OAuth2'
		],
		'Odnoklassniki' => [
			'type' => 'OAuth2'
		],
		'OpenID' => [
			'type' => 'OpenID'
		],
		'ORCID' => [
			'type' => 'OAuth2'
		],
		'Paypal' => [
			'type' => 'OpenID'
		],
		'PaypalOpenID' => [
			'type' => 'OpenID'
		],
		'QQ' => [
			'type' => 'OAuth2'
		],
		'Reddit' => [
			'type' => 'OAuth2'
		],
		'Slack' => [
			'type' => 'OAuth2'
		],
		'Spotify' => [
			'type' => 'OAuth2'
		],
		'StackExchange' => [
			'type' => 'OAuth2'
		],
		'StackExchangeOpenID' => [
			'type' => 'OpenID'
		],
		'Steam' => [
			'type' => 'Hybrid'
		],
		'Strava' => [
			'type' => 'OAuth2'
		],
		'SteemConnect' => [
			'type' => 'OAuth2'
		],
		'Telegram' => [
			'type' => 'Hybrid'
		],
		'Tumblr' => [
			'type' => 'OAuth1'
		],
		'TwitchTV' => [
			'type' => 'OAuth2'
		],
		'Twitter' => [
			'type' => 'OAuth1'
		],
		'Vkontakte' => [
			'type' => 'OAuth2'
		],
		'WeChat' => [
			'type' => 'OAuth2'
		],
		'WindowsLive' => [
			'type' => 'OAuth2'
		],
		'WordPress' => [
			'type' => 'OAuth2'
		],
		'Yandex' => [
			'type' => 'OAuth2'
		],
		'Yahoo' => [
			'type' => 'OAuth2'
		],
		'YahooOpenID' => [
			'type' => 'OpenID'
		]
	];

	public const DEFAULT_CONFIG = [
		'callback' => '',
		'providers' => [],
		'debug_mode' => false,
		'debug_file' => './oauth.log'
	];

	public const DEFAULT_PROVIDER_CONFIG = [
		'enabled' => false,
		'force' => false,
		'keys' => [
			'id' => '',
			'secret' => ''
		],
		'scope' => ''
	];

	public static function getConfigFilename() {
		return \ROOT . \DS . 'config' . \DS . self::CONFIG_FILE_NAME;
	}

	/**
	 *
	 * @return array
	 */
	public static function getAvailableProviders() {
		$provNames = array_keys(self::PROVIDERS);
		$providers = array_combine($provNames, $provNames);
		$actualProviders = array_keys(self::loadProvidersConfig());
		foreach ($actualProviders as $name) {
			if (isset($providers[$name])) {
				unset($providers[$name]);
			}
		}
		return $providers;
	}

	/**
	 *
	 * @return array
	 */
	public static function loadConfig(): array {
		if (is_array(self::$config)) {
			return self::$config;
		}
		$file = self::getConfigFilename();
		if (file_exists($file)) {
			return self::$config = include $file;
		}
		return self::$config = self::DEFAULT_CONFIG;
	}

	/**
	 *
	 * @return array
	 */
	public static function loadProvidersConfig(): array {
		$file = self::getConfigFilename();
		if (file_exists($file)) {
			$result = include $file;
		} else {
			$result = self::DEFAULT_CONFIG;
		}
		return $result['providers'] ?? [];
	}

	/**
	 *
	 * @return array
	 */
	public static function getProviderConfig(string $name): array {
		$default = self::DEFAULT_PROVIDER_CONFIG;
		$providerType = self::PROVIDERS[$name]['type'] ?? '';
		if ($providerType !== 'OAuth2' && $providerType !== 'OAuth1') {
			$default = [
				'enabled' => false,
				'force' => false
			];
		}
		return self::loadConfig()['providers'][$name] ?? $default;
	}

	/**
	 *
	 * @return array
	 */
	public static function getGlobalConfig(): array {
		$globalConfig = self::loadConfig();
		unset($globalConfig['providers']);
		return $globalConfig;
	}

	/**
	 *
	 * @param array $config
	 * @return number
	 */
	public static function saveConfig(array $config) {
		$content = "<?php\nreturn " . UArray::asPhpArray($config, "array", 1, true) . ";";
		self::$config = $config;
		return UFileSystem::save(self::getConfigFilename(), $content);
	}

	/**
	 *
	 * @param string $name
	 * @param array $config
	 * @return number
	 */
	public static function addAndSaveProvider(string $name, array $config) {
		$globalConfig = self::loadConfig();
		$globalConfig['providers'][$name] = $config;
		return self::saveConfig($globalConfig);
	}

	/**
	 *
	 * @param string $name
	 * @return number
	 */
	public static function toggleAndSaveProvider(string $name) {
		$globalConfig = self::loadConfig();
		$globalConfig['providers'][$name]['enabled'] = ! (($globalConfig['providers'][$name]['enabled']) ?? false);
		return self::saveConfig($globalConfig);
	}

	/**
	 *
	 * @param string $name
	 * @param string $key
	 * @param mixed $value
	 * @return number
	 */
	public static function updateProviderValue(string $name, string $key, $value = null) {
		$globalConfig = self::loadConfig();
		if (isset($value)) {
			$globalConfig['providers'][$name][$key] = $value;
		} else {
			if (isset($globalConfig['providers'][$name])) {
				unset($globalConfig['providers'][$name]);
			}
		}

		return self::saveConfig($globalConfig);
	}

	/**
	 *
	 * @param string $name
	 * @return number
	 */
	public static function removeAndSaveProvider(string $name) {
		$globalConfig = self::loadConfig();
		if (isset($globalConfig['providers'][$name])) {
			unset($globalConfig['providers'][$name]);
		}
		return self::saveConfig($globalConfig);
	}

	/**
	 *
	 * @param string $siteUrl
	 * @return string
	 */
	public static function getRedirectRoute(?string $siteUrl = null): string {
		if (! isset($siteUrl)) {
			$siteUrl = $GLOBALS['config']['siteUrl'];
		}
		$globalConfig = self::loadConfig();
		$callback = $globalConfig['callback'] ?? '';
		return \trim(\str_replace($siteUrl, '', $callback), '/');
	}

	/**
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getProviderType(string $name) {
		return self::PROVIDERS[$name]['type'] ?? 'OAuth2';
	}
}

