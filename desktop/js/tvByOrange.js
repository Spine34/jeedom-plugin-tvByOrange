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

/* Permet la réorganisation des commandes dans l'équipement */
// $("#table_cmd").sortable({
// 	axis: "y",
// 	cursor: "move",
// 	items: ".cmd",
// 	placeholder: "ui-state-highlight",
// 	tolerance: "intersect",
// 	forcePlaceholderSize: true
// });

var tableChannel = document.getElementById('table_channel');
new Sortable(tableChannel.tBodies[0], {
	delay: 100,
	delayOnTouchOnly: true,
	touchStartThreshold: 20,
	draggable: 'tr.cmd',
	filter: 'a, input, textarea, label, select',
	preventOnFilter: false,
	direction: 'vertical',
	chosenClass: 'dragSelected',
	onEnd: function (event) {
		jeeFrontEnd.modifyWithoutSave = true;
		modifyWithoutSave = true;
	},
});

/* Fonction permettant l'affichage des commandes dans l'équipement */
// function addCmdToTable(_cmd) {
// 	if (!isset(_cmd)) {
// 		var _cmd = { configuration: {} };
// 	}
// 	if (!isset(_cmd.configuration)) {
// 		_cmd.configuration = {};
// 	}
// 	if (_cmd.configuration['table'] == 'cmd') {
// 		addCmdToTableCmd(_cmd);
// 	}
// 	if (_cmd.configuration['table'] == 'channel') {
// 		addCmdToTableChannel(_cmd);
// 	}
// }

function addCmdToTable(_cmd) {
	// if (!isset(_cmd)) {
	// 	var _cmd = { configuration: {} };
	// }
	// if (!isset(_cmd.configuration)) {
	// 	_cmd.configuration = {};
	// }
	if (_cmd.configuration['table'] == 'cmd') {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td class="hidden-xs">';
		tr += '<span class="cmdAttr" data-l1key="id"></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<div class="input-group">';
		tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">';
		tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>';
		tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>';
		tr += '</div>';
		tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}" disabled>';
		tr += '<option value="">{{Aucune}}</option>';
		tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<fieldset style="margin: unset;" disabled>';
		tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
		tr += '</fieldset>';
		tr += '</td>';
		tr += '<td>';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> ';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> ';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> ';
		tr += '<div style="margin-top:7px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '</div>';
		tr += '</td>';
		if (typeof _cmd.configuration.key === 'undefined') {
			tr += '<td>';
			tr += '</td>';
		} else {
			tr += '<td>';
			tr += '<input type="number" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="key" disabled>';
			tr += '</td>';
		}
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
		tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
		}
		tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>';
		tr += '</tr>';

		// $('#table_cmd tbody').append(tr);
		// var tr = $('#table_cmd tbody tr').last();
		// jeedom.eqLogic.buildSelectCmd({
		// 	id: $('.eqLogicAttr[data-l1key=id]').value(),
		// 	filter: { type: 'info' },
		// 	error: function (error) {
		// 		$('#div_alert').showAlert({ message: error.message, level: 'danger' });
		// 	},
		// 	success: function (result) {
		// 		tr.find('.cmdAttr[data-l1key=value]').append(result);
		// 		tr.setValues(_cmd, '.cmdAttr');
		// 		jeedom.cmd.changeType(tr, init(_cmd.subType));
		// 	}
		// });

		let newRow = document.createElement('tr');
		newRow.innerHTML = tr;
		newRow.addClass('cmd');
		newRow.setAttribute('data-cmd_id', init(_cmd.id));
		document.getElementById('table_cmd').querySelector('tbody').appendChild(newRow);

		jeedom.eqLogic.buildSelectCmd({
			id: document.querySelector('.eqLogicAttr[data-l1key="id"]').jeeValue(),
			filter: { type: 'info' },
			error: function (error) {
				jeedomUtils.showAlert({ message: error.message, level: 'danger' });
			},
			success: function (result) {
				newRow.querySelector('.cmdAttr[data-l1key="value"]').insertAdjacentHTML('beforeend', result);
				newRow.setJeeValues(_cmd, '.cmdAttr');
				jeedom.cmd.changeType(newRow, init(_cmd.subType));
			}
		});
	} else if (_cmd.configuration['table'] == 'channel') {
		var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
		tr += '<td class="hidden-xs">';
		tr += '<span class="cmdAttr" data-l1key="id"></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<div class="input-group">';
		tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">';
		tr += '<span class="input-group-btn"><a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a></span>';
		tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>';
		tr += '</div>';
		tr += '<select class="cmdAttr form-control input-sm" data-l1key="value" style="display:none;margin-top:5px;" title="{{Commande info liée}}" disabled>';
		tr += '<option value="">{{Aucune}}</option>';
		tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<fieldset style="margin: unset;" disabled>';
		tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
		tr += '</fieldset>';
		tr += '</td>';
		tr += '<td>';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</label> ';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked/>{{Historiser}}</label> ';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="display" data-l2key="invertBinary"/>{{Inverser}}</label> ';
		tr += '<div style="margin-top:7px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">';
		tr += '</div>';
		tr += '</td>';
		tr += '<td>';
		tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="customChannelSelect"/>{{Sélectionner}}</label>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input type="number" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="number">';
		tr += '</td>';
		tr += '<td>';
		tr += '<input type="number" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="epg_id">';
		tr += '</td>';
		// tr += '<td>';
		// tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
		// tr += '</td>';
		tr += '<td>';
		if (is_numeric(_cmd.id)) {
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
			tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
		}
		tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove" title="{{Supprimer la commande}}"></i></td>';
		tr += '<td>';
		tr += '<input type="text" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="table" style="display:none;">';
		tr += '</td>';
		tr += '</tr>';

		// $('#table_cmd tbody').append(tr);
		// var tr = $('#table_cmd tbody tr').last();
		// jeedom.eqLogic.buildSelectCmd({
		// 	id: $('.eqLogicAttr[data-l1key=id]').value(),
		// 	filter: { type: 'info' },
		// 	error: function (error) {
		// 		$('#div_alert').showAlert({ message: error.message, level: 'danger' });
		// 	},
		// 	success: function (result) {
		// 		tr.find('.cmdAttr[data-l1key=value]').append(result);
		// 		tr.setValues(_cmd, '.cmdAttr');
		// 		jeedom.cmd.changeType(tr, init(_cmd.subType));
		// 	}
		// });

		let newRow = document.createElement('tr');
		newRow.innerHTML = tr;
		newRow.addClass('cmd');
		newRow.setAttribute('data-cmd_id', init(_cmd.id));
		document.getElementById('table_channel').querySelector('tbody').appendChild(newRow);

		jeedom.eqLogic.buildSelectCmd({
			id: document.querySelector('.eqLogicAttr[data-l1key="id"]').jeeValue(),
			filter: { type: 'info' },
			error: function (error) {
				jeedomUtils.showAlert({ message: error.message, level: 'danger' });
			},
			success: function (result) {
				newRow.querySelector('.cmdAttr[data-l1key="value"]').insertAdjacentHTML('beforeend', result);
				newRow.setJeeValues(_cmd, '.cmdAttr');
				jeedom.cmd.changeType(newRow, init(_cmd.subType));
			}
		});
	}
}

document.getElementById('addChannel').addEventListener('click', function (event) {
	addCmdToTable({ type: 'action', subType: 'other', configuration: { table: 'channel' } });
	modifyWithoutSave = true;
});

document.getElementById('orderChannel').addEventListener('click', function (event) {
	domUtils.ajax({
		type: "POST",
		url: "plugins/tvByOrange/core/ajax/tvByOrange.ajax.php",
		data: {
			action: "orderChannel",
			eqLogicId: document.querySelector('.eqLogicAttr[data-l1key="id"]').jeeValue()
		},
		dataType: 'json',
		global: false,
		error: function (error) {
			jeedomUtils.showAlert({
				message: error.message,
				level: 'danger'
			});
		},
		success: function (data) {
			jeedomUtils.reloadPagePrompt('{{Liste de chaînes ordonnée avec succès.}}');
		}
	});
});