<?php
namespace Ubiquity\client\oauth;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;

class OAuthAdmin {

	private static $config;

	private const OAUTH1 = 'OAuth1';

	private const OAUTH2 = 'OAuth2';

	private const OPENID = 'OpenID';

	private const HYBRID = 'Hybrid';

	public const CONFIG_FILE_NAME = 'oauth.php';

	public const PROVIDERS = [
		'Amazon' => [
			'type' => self::OAUTH2,
			'dev' => 'https://developer.amazon.com'
		],
		'AOLOpenID' => [
			'type' => self::OPENID
		],
		'Authentiq' => [
			'type' => self::OAUTH2
		],
		'BitBucket' => [
			'type' => self::OAUTH2
		],
		'Blizzard' => [
			'type' => self::OAUTH2
		],
		'Discord' => [
			'type' => self::OAUTH2
		],
		'Disqus' => [
			'type' => self::OAUTH2
		],
		'Dribbble' => [
			'type' => self::OAUTH2
		],
		'Facebook' => [
			'type' => self::OAUTH2
		],
		'Foursquare' => [
			'type' => self::OAUTH2
		],
		'GitHub' => [
			'type' => self::OAUTH2
		],
		'GitLab' => [
			'type' => self::OAUTH2
		],
		'Google' => [
			'type' => self::OAUTH2,
			'dev' => 'https://console.developers.google.com/'
		],
		'Instagram' => [
			'type' => self::OAUTH2
		],
		'LinkedIn' => [
			'type' => self::OAUTH2
		],
		'Mailru' => [
			'type' => self::OAUTH2
		],
		'MicrosoftGraph' => [
			'type' => self::OAUTH2
		],
		'Odnoklassniki' => [
			'type' => self::OAUTH2
		],
		'OpenID' => [
			'type' => self::OPENID
		],
		'ORCID' => [
			'type' => self::OAUTH2
		],
		'Paypal' => [
			'type' => self::OPENID
		],
		'PaypalOpenID' => [
			'type' => self::OPENID
		],
		'QQ' => [
			'type' => self::OAUTH2
		],
		'Reddit' => [
			'type' => self::OAUTH2
		],
		'Slack' => [
			'type' => self::OAUTH2
		],
		'Spotify' => [
			'type' => self::OAUTH2
		],
		'StackExchange' => [
			'type' => self::OAUTH2
		],
		'StackExchangeOpenID' => [
			'type' => self::OPENID
		],
		'Steam' => [
			'type' => self::HYBRID
		],
		'Strava' => [
			'type' => self::OAUTH2
		],
		'SteemConnect' => [
			'type' => self::OAUTH2
		],
		'Telegram' => [
			'type' => self::HYBRID
		],
		'Tumblr' => [
			'type' => self::OAUTH1
		],
		'TwitchTV' => [
			'type' => self::OAUTH2
		],
		'Twitter' => [
			'type' => self::OAUTH1
		],
		'Vkontakte' => [
			'type' => self::OAUTH2
		],
		'WeChat' => [
			'type' => self::OAUTH2
		],
		'WindowsLive' => [
			'type' => self::OAUTH2
		],
		'WordPress' => [
			'type' => self::OAUTH2
		],
		'Yandex' => [
			'type' => self::OAUTH2
		],
		'Yahoo' => [
			'type' => self::OAUTH2
		],
		'YahooOpenID' => [
			'type' => self::OPENID
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
	public static function getEnabledProviders() {
		$result = [];
		$actualProviders = self::loadProvidersConfig();
		foreach ($actualProviders as $name => $config) {
			if (isset($config['enabled']) && $config['enabled'] === true) {
				$result[strtolower($name)] = $name;
			}
		}
		return $result;
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
		if ($providerType !== self::OAUTH1 && $providerType !== self::OAUTH2) {
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
		return self::PROVIDERS[$name]['type'] ?? self::OAUTH2;
	}
}

