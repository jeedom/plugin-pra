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
include_file('core', 'authentification', 'php');
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<form class="form-horizontal">
	<fieldset>
		<legend>{{Mes infos}}</legend>
		<div class="form-group">
			<label class="col-lg-1 control-label">{{IP Interne}}</label>
			<div class="col-lg-2">
				<span class="label label-primary" style="font-size:1em;"><?php echo network::getNetworkAccess('internal', 'ip') ?></span>
			</div>
			<label class="col-lg-1 control-label">{{Clef API}}</label>
			<div class="col-lg-3">
				<span class="label label-primary" style="font-size:1em;"><?php echo jeedom::getApiKey('pra') ?></span>
			</div>
		</div>

		<legend>{{Noeud 1}}</legend>
		<div class="form-group">
			<label class="col-lg-1 control-label">{{IP}}</label>
			<div class="col-lg-2">
				<input class="configKey form-control" data-l1key="node::1::ip" />
			</div>
			<label class="col-lg-1 control-label">{{Clef API}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="node::1::apikey" />
			</div>
			<label class="col-lg-2 control-label">{{Etat par défaut}}</label>
			<div class="col-lg-3">
				<select class="configKey form-control" data-l1key="node::1::state">
					<option value="primary">{{Primaire}}</option>
					<option value="secondary">{{Secondaire}}</option>
				</select>
			</div>
		</div>

		<legend>{{Noeud 2}}</legend>
		<div class="form-group">
			<label class="col-lg-1 control-label">{{IP}}</label>
			<div class="col-lg-2">
				<input class="configKey form-control" data-l1key="node::2::ip" />
			</div>
			<label class="col-lg-1 control-label">{{Clef API}}</label>
			<div class="col-lg-3">
				<input class="configKey form-control" data-l1key="node::2::apikey" />
			</div>
			<label class="col-lg-2 control-label">{{Etat par défaut}}</label>
			<div class="col-lg-3">
				<select class="configKey form-control" data-l1key="node::2::state">
					<option value="primary">{{Primaire}}</option>
					<option value="secondary">{{Secondaire}}</option>
				</select>
			</div>
		</div>
	</fieldset>
</form>

