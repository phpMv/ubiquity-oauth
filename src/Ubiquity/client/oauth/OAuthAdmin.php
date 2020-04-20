<?php
namespace Ubiquity\client\oauth;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;

class OAuthAdmin {

	private static $config;

	public const CONFIG_FILE_NAME = 'oauth.php';

	public const PROVIDERS = [
		'Amazon',
		'AOLOpenID',
		'Authentiq',
		'BitBucket',
		'Blizzard',
		'Discord',
		'Disqus',
		'Dribbble',
		'Facebook',
		'Foursquare',
		'GitHub',
		'GitLab',
		'Google',
		'Instagram',
		'LinkedIn',
		'Mailru',
		'MicrosoftGraph',
		'Odnoklassniki',
		'OpenID',
		'ORCID',
		'Paypal',
		'PaypalOpenID',
		'QQ',
		'Reddit',
		'Slack',
		'Spotify',
		'StackExchange',
		'StackExchangeOpenID',
		'Steam',
		'Strava',
		'SteemConnect',
		'Telegram',
		'Tumblr',
		'TwitchTV',
		'Twitter',
		'Vkontakte',
		'WeChat',
		'WindowsLive',
		'WordPress',
		'Yandex',
		'Yahoo',
		'YahooOpenID'
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
		$providers = array_combine(self::PROVIDERS, self::PROVIDERS);
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
		return self::loadConfig()['providers'][$name] ?? self::DEFAULT_PROVIDER_CONFIG;
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
}

