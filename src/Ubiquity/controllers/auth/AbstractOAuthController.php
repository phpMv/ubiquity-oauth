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
		$this->provider = OAuthManager::startAdapter($name);
		$this->onConnect($name, $this->provider);
	}

	abstract protected function onConnect(string $name, AdapterInterface $provider);
}

