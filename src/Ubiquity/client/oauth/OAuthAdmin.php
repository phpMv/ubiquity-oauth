<?php
namespace Ubiquity\client\oauth;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;

class OAuthAdmin {

	public const CONFIG_FILE_NAME = 'oauth.php';

	public const PROVIDERS = [
		'Amazon',
		'Discord',
		'Facebook',
		'GitHub',
		'GitLab',
		'Google',
		'Instagram',
		'LinkedIn',
		'Twitter'
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
	public static function loadConfig(): array {
		$file = self::getConfigFilename();
		if (file_exists($file)) {
			return include $file;
		}
		return OAuthAdmin::DEFAULT_CONFIG;
	}

	public static function saveConfig(array $config) {
		$content = "<?php\nreturn " . UArray::asPhpArray($config, "array", 1, true) . ";";
		return UFileSystem::save(self::getConfigFilename(), $content);
	}

	public static function addAndSaveProvider($name, $config) {
		$globalConfig = self::loadConfig();
		$globalConfig['providers'][$name] = $config;
		return self::saveConfig($globalConfig);
	}
}

