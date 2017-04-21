<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class pra extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	public static function create($_node = 1) {
		$pra = self::byLogicalId('node' . $_node, 'pra');
		if (!is_object($pra)) {
			$pra = new pra();
		}
		$pra->setLogicalId('node' . $_node);
		$pra->setConfiguration('node', $_node);
		$pra->setName('Noeud ' . $_node);
		$pra->setEqType_name('pra');
		$pra->setIsVisible(0);
		$pra->setIsEnable(1);
		$pra->save();
	}

	public static function toggle() {
		$node1 = self::byLogicalId('node1', 'pra');
		$node2 = self::byLogicalId('node2', 'pra');
		if (config::byKey('node::1::state', 'pra', '', true) == 'primary') {
			$node1->toSecondary();
			$node2->toPrimary();
		} else {
			$node2->toSecondary();
			$node1->toPrimary();
		}
		$node2->refreshData();
		$node1->refreshData();
	}

	public static function cron5() {
		$node1 = self::byLogicalId('node1', 'pra');
		$node2 = self::byLogicalId('node2', 'pra');
		$node2->refreshData();
		$node1->refreshData();
		if ($node1->itsme() && config::byKey('node::1::state', 'pra', '', true) == 'secondary' && !$node2->isOnline()) {
			log::add('pra', 'error', __('Bascule des serveur primaire/secondaire', __FILE__));
			self::toggle();
		}
		if ($node2->itsme() && config::byKey('node::2::state', 'pra', '', true) == 'secondary' && !$node2->isOnline()) {
			log::add('pra', 'error', __('Bascule des serveur primaire/secondaire', __FILE__));
			self::toggle();
		}
	}

	/*     * *********************Méthodes d'instance************************* */

	public function getOtherNode() {
		if ($this->getConfiguration('node') == 1) {
			return 2;
		}
		return 1;
	}

	public function getJsonRpc($_params = array()) {
		$params = array(
			'apikey' => config::byKey('node::' . $this->getConfiguration('node') . '::apikey'),
		);
		$params = array_merge($params, $_params);
		$jsonrpc = new jsonrpcClient(config::byKey('node::' . $this->getConfiguration('node') . '::ip') . '/core/api/jeeApi.php', '', $params);
		$jsonrpc->setNoSslCheck(true);
		return $jsonrpc;
	}

	public function toSecondary() {
		config::save('node::' . $this->getConfiguration('node') . '::state', 'secondary', 'pra');
		if (!$this->itsme()) {
			$jsonrpc = $this->getJsonRpc(array('plugin' => 'pra'));
			return $jsonrpc->sendRequest('toSecondary', array('node' => 'node::' . $this->getConfiguration('node') . '::ip'));
		}
	}

	public function toPrimary() {
		config::save('node::' . $this->getConfiguration('node') . '::state', 'primary', 'pra');
		if (!$this->itsme()) {
			$jsonrpc = $this->getJsonRpc(array('plugin' => 'pra'));
			return $jsonrpc->sendRequest('toPrimary', array('node' => 'node::' . $this->getConfiguration('node') . '::ip'));
		}
	}

	public function postSave() {
		$cmd = $this->getCmd('info', 'Mode');
		if (!is_object($cmd)) {
			$cmd = new praCmd();
			$cmd->setName(__('Mode', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubtype('string');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setIsVisible(1);
		$cmd->setLogicalId('mode');
		$cmd->save();

		$cmd = $this->getCmd('info', 'ip');
		if (!is_object($cmd)) {
			$cmd = new praCmd();
			$cmd->setName(__('ip', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubtype('string');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setIsVisible(1);
		$cmd->setLogicalId('ip');
		$cmd->save();

		$cmd = $this->getCmd('info', 'state');
		if (!is_object($cmd)) {
			$cmd = new praCmd();
			$cmd->setName(__('Status', __FILE__));
		}
		$cmd->setType('info');
		$cmd->setSubtype('binary');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setIsVisible(1);
		$cmd->setLogicalId('state');
		$cmd->save();

		$cmd = $this->getCmd('action', 'toggle');
		if (!is_object($cmd)) {
			$cmd = new praCmd();
			$cmd->setName(__('Bascule', __FILE__));
		}
		$cmd->setType('action');
		$cmd->setSubtype('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setIsVisible(1);
		$cmd->setLogicalId('toggle');
		$cmd->save();

		$this->refreshData();
	}

	public function refreshData() {
		$this->checkAndUpdateCmd('ip', config::byKey('node::' . $this->getConfiguration('node') . '::ip', 'pra', '', true));
		$this->checkAndUpdateCmd('state', $this->isOnline());
		$this->checkAndUpdateCmd('mode', config::byKey('node::' . $this->getConfiguration('node') . '::state', 'pra', '', true));
	}

	public function isOnline() {
		if ($this->itsme()) {
			return true;
		}
		$jsonrpc = $this->getJsonRpc();
		return $jsonrpc->sendRequest('ping');
	}

	public function itsme() {
		return (trim($this->getCmd(null, 'ip')->execCmd()) == network::getNetworkAccess('internal', 'ip'));
	}

	/*     * **********************Getteur Setteur*************************** */
}

class praCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function execute($_options = array()) {
		if ($this->getLogicalID() == 'toggle') {
			pra::toggle();
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}

?>
