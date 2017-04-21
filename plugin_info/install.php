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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function pra_install() {
	if (config::byKey('node::1::ip', 'pra') == '') {
		config::save('node::1::ip', network::getNetworkAccess('internal', 'ip'), 'pra');
	}
	if (config::byKey('node::1::apikey', 'pra') == '') {
		config::save('node::1::apikey', jeedom::getApiKey('pra'), 'pra');
	}
	if (config::byKey('node::1::state', 'pra') == '') {
		config::save('node::1::state', 'primary', 'pra');
	}
	if (config::byKey('node::2::state', 'pra') == '') {
		config::save('node::2::state', 'secondary', 'pra');
	}
	pra::create(1);
	pra::create(2);
}

function pra_update() {

}

function pra_remove() {

}

?>
