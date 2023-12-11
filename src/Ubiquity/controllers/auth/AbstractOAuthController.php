<?php
namespace Ubiquity\controllers\auth;

use Ubiquity\controllers\Controller;
use Ubiquity\client\oauth\OAuthManager;
use Hybridauth\Adapter\AdapterInterface;

/**
 * Ubiquity\controllers\auth$OAuthController
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.1
 *
 */
abstract class AbstractOAuthController extends Controller {

	/**
	 *
	 * @var AdapterInterface
	 */
	protected $provider;

	public function _oauth(string $name, ?string $callbackUrl = null) {
        if(!isset($callbackUrl)) {
            $requestURI = \trim(\strtok($_SERVER["REQUEST_URI"], '?'), '/');
            $callbackUrl = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ((isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/{$requestURI}";
        }
		$this->provider = OAuthManager::startAdapter($name, $callbackUrl);
		$this->onConnect($name, $this->provider);
	}

	abstract protected function onConnect(string $name, AdapterInterface $provider);
}

