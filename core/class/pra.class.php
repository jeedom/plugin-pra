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

	/*     * *********************Méthodes d'instance************************* */

	public function getJsonRpc() {
		$params = array(
			'apikey' => config::byKey('node::' . $this->getConfiguration('node') . '::apikey'),
		);
		$jsonrpc = new jsonrpcClient(config::byKey('node::' . $this->getConfiguration('node') . '::ip') . '/core/api/jeeApi.php', '', $params);
		$jsonrpc->setNoSslCheck(true);
		return $jsonrpc;
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

		$cmd = $this->getCmd('action', 'switch');
		if (!is_object($cmd)) {
			$cmd = new praCmd();
			$cmd->setName(__('Bascule', __FILE__));
		}
		$cmd->setType('action');
		$cmd->setSubtype('other');
		$cmd->setEqLogic_id($this->getId());
		$cmd->setIsVisible(1);
		$cmd->setLogicalId('switch');
		$cmd->save();

		$this->resfresh();
	}

	public function resfresh() {
		$this->checkAndUpdateCmd('ip', config::byKey('node::' . $this->getConfiguration('node') . '::ip', 'pra'));
		$this->checkAndUpdateCmd('state', $this->isOnline());
		$this->checkAndUpdateCmd('mode', config::byKey('node::' . $this->getConfiguration('node') . '::state', 'pra'));
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

	}

	/*     * **********************Getteur Setteur*************************** */
}

?>
