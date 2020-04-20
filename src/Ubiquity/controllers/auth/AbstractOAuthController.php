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
 * @version 1.0.0
 *
 */
abstract class AbstractOAuthController extends Controller {

	/**
	 *
	 * @var AdapterInterface
	 */
	protected $provider;

	public function _oauth(string $name) {
		$link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
		$this->provider = OAuthManager::startAdapter($name, $link);
		$this->onConnect($name, $this->provider);
	}

	abstract protected function onConnect(string $name, AdapterInterface $provider);
}

