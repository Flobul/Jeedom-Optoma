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

$("#table_info").delegate(".listEquipementInfo", 'click', function () {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
        calcul.atCaret('insert', result.human);
    });
});

$("#table_cmd").delegate(".listEquipementAction", 'click', function () {
    var el = $(this);
    var subtype = $(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').value();
    jeedom.cmd.getSelectModal({cmd: {type: 'action', subType: subtype}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.attr('data-input') + ']');
        calcul.atCaret('insert', result.human);
    });
});

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_info").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

$('#bt_weboptoma').on('click', function () {
  var nodeId = $('#idipoptoma').value();
  $('#md_modal').dialog({title: "{{Interface Optoma}}"});
  $('#md_modal').load('index.php?v=d&plugin=Optoma&modal=web&ip=' + nodeId).dialog('open');
});

function amxDeviceDiscovery(_state) {
	$.ajax({
		type: "POST",
		url: "plugins/Optoma/core/ajax/Optoma.ajax.php",
		data: {
			action: "amxDeviceDiscovery",
			state: _state,
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function (data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if ($('.eqLogicAttr[data-l1key=configuration][data-l2key=IP]').value() != '') {
				bootbox.confirm('{{Voulez-vous écraser l\'adresse IP actuelle ?}}', function (result) {
				if (result) {
					$('.eqLogicAttr[data-l1key=configuration][data-l2key=IP]').value(data.result);
				}
				});
			}
			else {
				$('.eqLogicAttr[data-l1key=configuration][data-l2key=IP]').value(data.result);
			}
		}
	});
}

 function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    if (init(_cmd.logicalId) == 'refresh') {
		return;
	}

	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  if (init(_cmd.type) == 'info') {
		tr += '<td><div style="display:inline-flex;">';
		tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> {{Icône}}</a>';
		tr += '<span class="cmdAttr" data-l1key="display" data-l2key="icon"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}}"></div>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-top:-5px;width:40%;display:inline-block;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="margin-top:-5px;width:50%;display:inline-block;" ></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input readonly class="cmdAttr" id="'+ _cmd.id +'value" style="width:200px;font-style:italic;">';
		$('#'+_cmd.id +'value').val("loading");
		jeedom.cmd.execute({
			id: _cmd.id,
			cache: 0,
			notify: false,
			success: function(result) {
				$('#'+_cmd.id +'value').val(result);
			}
		});
		tr += '</td>';
		tr += '<td>';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span></br> ';
        if (init(_cmd.subType) == 'numeric') {
            tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="display:inline-block;width:50px;"></input>';
            tr += '<style>.select {}</style>';
            tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="display:inline-block;width:50px;"></input>';
        }
		tr += '</td>';
		tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible tooltips" title="Configuration de la commande" data-action="configure"><i class="fa fa-cogs"></i></a> ';
            tr += '<a class="btn btn-default btn-xs cmdAction tooltips" title="Test de la commande" data-action="test"><i class="fa fa-rss"></i></a>';
            tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" title="Supprimer de la commande" data-action="remove"></i></td>';
        }
        tr += '</td>';

    $('#table_info tbody').append(tr);
    $('#table_info tbody tr:last').setValues(_cmd, '.cmdAttr');
    }
	if (init(_cmd.type) == 'action') {
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="margin-top:0;width:70%;display:inline-block;" placeholder="{{Nom}}" >';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-top:-5px;width:40%;display:inline-block;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="margin-top:-5px;width:50%;display:inline-block;"></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="margin-top:0;width:40%;display:inline-block;" title="{{La valeur de la commande action vaut par défaut la commande information}}">';
		tr += '<option value="">Aucune</option>';
		tr += '</select>';
        tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="updateCmdId" style="margin-top:5px;margin-left:5px;width:40%;display:inline-block;" title="{{Commande d\'information à mettre à jour}}">';
        tr += '<option value="">Aucune</option>';
        tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
  tr += '</td>';
      tr += '</td>';
		tr += '<td>';
    if (is_numeric(_cmd.id)) {
		tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible tooltips" title="Configuration de la commande" data-action="configure"><i class="fa fa-cogs"></i></a> ';
		tr += '<a class="btn btn-default btn-xs cmdAction tooltips" title="Test de la commande" data-action="test"><i class="fa fa-rss"></i></a>';
		tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" title="Supprimer de la commande" data-action="remove"></i></td>';
    }
		tr += '</td>';

    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');

	}
    tr += '</tr>';

	if (init(_cmd.type) == 'info') {
		if (isset(_cmd.type)) {
			$('#table_info tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
		}
		jeedom.cmd.changeType($('#table_info tbody tr:last'), init(_cmd.subType));
	}
	if (init(_cmd.type) == 'action') {
        var tr = $('#table_cmd tbody tr:last');
		jeedom.eqLogic.builSelectCmd({
			id: $('.eqLogicAttr[data-l1key=id]').value(),
			filter: {type: 'info'},
			error: function (error) {
				$('#div_alert').showAlert({message: error.message, level: 'danger'});
			},
			success: function (result) {
				tr.find('.cmdAttr[data-l1key=value]').append(result);
		        tr.find('.cmdAttr[data-l1key=configuration][data-l2key=updateCmdId]').append(result);
				tr.setValues(_cmd, '.cmdAttr');
				jeedom.cmd.changeType(tr, init(_cmd.subType));
			}
		});
	}
}

$('.changeIncludeState').on('click', function () {
	var el = $(this);
	var state = $(this).attr('data-state');
	amxDeviceDiscovery(state);
	jeedom.config.save({
		plugin : 'Optoma',
		configuration: {include_mode: el.attr('data-state')},
		error: function (error) {
			$('#div_alert').showAlert({message: error.message, level: 'danger'});
		},
		success: function () {
			if (el.attr('data-state') == 1) {
				$.hideAlert();
				$('.changeIncludeState:not(.card)').removeClass('btn-default').addClass('btn-success');
				$('.changeIncludeState').attr('data-state', 0);
				$('.changeIncludeState.card').css('background-color','#8000FF');
				$('.changeIncludeState.card span center').text('{{Arrêter l\'inclusion}}');
				$('.changeIncludeState:not(.card)').html('<i class="fa fa-sign-in fa-rotate-90"></i> {{Arrêter l\'inclusion}}');
				$('#div_inclusionAlert').showAlert({message: '{{Vous êtes en mode inclusion. Cliquez à nouveau sur le bouton d\'inclusion pour sortir de ce mode}}', level: 'warning'});
			} else {
				$.hideAlert();
				$('.changeIncludeState:not(.card)').addClass('btn-default').removeClass('btn-success btn-danger');
				$('.changeIncludeState').attr('data-state', 1);
				$('.changeIncludeState:not(.card)').html('<i class="fa fa-sign-in fa-rotate-90"></i> {{Mode inclusion}}');
				$('.changeIncludeState.card span center').text('{{Mode inclusion}}');
				$('.changeIncludeState.card').css('background-color','#ffffff');
				$('#div_inclusionAlert').hideAlert();
			}
		}
	});
});

$('body').on('Optoma::includeDevice', function (_event,_options) {
  if (modifyWithoutSave) {
    $('#div_inclusionAlert').showAlert({message: '{{Un périphérique vient d\'être inclu/exclu. Veuillez réactualiser la page}}', level: 'warning'});
  } else {
    if (_options == '') {
      window.location.reload();
    } else {
      window.location.href = 'index.php?v=d&p=Optoma&m=Optoma&id=' + _options;
    }
  }
});

$('#bt_healthoptoma').on('click', function () {
    $('#md_modal').dialog({title: "{{Santé Optoma}}"});
    $('#md_modal').load('index.php?v=d&plugin=Optoma&modal=health').dialog('open');
});

$('#bt_documentationOptoma').off('click').on('click', function() {
    window.open($(this).attr("data-location"), "_blank", null);
});

function getConfFile(_eqLogic) {

  $.ajax({
    type: "POST",
    url: "plugins/Optoma/core/ajax/Optoma.ajax.php",
    data: {
      action: "getConfFile",
      id: _eqLogic.id,
      type: _eqLogic.configuration.type,
    },
    dataType: 'json',
    global: false,
    error: function(request, status, error) {
      handleAjaxError(request, status, error);
    },
    success: function(data) {
      $('.eqLogicAttr[data-l1key=configuration][data-l2key=fileconf]').empty();
      if (data.result.length > 1) {
        var option = '';
        option += '<option value="' + data.result + '">' + data.result + '</option>';
        $('.eqLogicAttr[data-l1key=configuration][data-l2key=fileconf]').append(option);
        $('.eqLogicAttr[data-l1key=configuration][data-l2key=fileconf]').show();
        if (isset(_eqLogic.configuration.fileconf)) {
          $('.eqLogicAttr[data-l1key=configuration][data-l2key=fileconf]').value(_eqLogic.configuration.fileconf);
        }
      } else {
        $('.eqLogicAttr[data-l1key=configuration][data-l2key=fileconf]').hide();
      }
      modifyWithoutSave = false;
    }
  });
}

$('.eqLogicAttr[data-l1key=configuration][data-l2key=actionMethod], .eqLogicAttr[data-l1key=configuration][data-l2key=infoMethod]').change(function () {
    var infoMethod = $('.eqLogicAttr[data-l1key=configuration][data-l2key=infoMethod]').value();
    var actionMethod = $('.eqLogicAttr[data-l1key=configuration][data-l2key=actionMethod]').value();

    if(infoMethod == 'API' || actionMethod == 'API' || infoMethod == 'API-TELNET' || actionMethod == 'API-TELNET') {
        $('#APIgroup').show();
    } else {
        $('#APIgroup').hide();
    }
    if(infoMethod == 'TELNET' || actionMethod == 'TELNET' || infoMethod == 'API-TELNET' || actionMethod == 'API-TELNET') {
        $('#TELNETgroup').show();
    } else {
        $('#TELNETgroup').hide();
    }
});


function printEqLogic(_eqLogic) {

    printEqLogicTab(_eqLogic); //affiche les info de l'équipement
    $('body').setValues(_eqLogic, '.eqLogicAttr');
    //initCheckBox();
    modifyWithoutSave = false;
}

function printEqLogicTab(_eqLogic) {

    $('#table_infoseqlogic tbody').empty();

    //affichage des configurations du device
    printEqLogicHelper("{{Type}}", "type", _eqLogic);
    printEqLogicHelper("{{Modèle}}", "model", _eqLogic);
    printEqLogicHelper("{{Adresse MAC}}", "MAC", _eqLogic);
    printEqLogicHelper("{{Version RS232}}", "RS232Version", _eqLogic);
    printEqLogicHelper("{{Version LAN}}", "LANFirmwareVersion", _eqLogic);
    printEqLogicHelper("{{Version Firmware}}", "SoftwareVersion", _eqLogic);
    printEqLogicHelper("{{Découverte auto}}", "auto_discovery", _eqLogic);
    printEqLogicHelper("{{Ports ouverts}}", "openPorts", _eqLogic);

    if (isset(_eqLogic.configuration.model) && _eqLogic.configuration.model !== undefined) {
        $('#img_device').attr("src", 'plugins/Optoma/core/config/img/' + _eqLogic.configuration.model + '.png');
    }
}

function printEqLogicHelper(_label, _name, _eqLogic) {

    if (isset(_eqLogic.result)) {
        var eqLogic = _eqLogic.result;
    } else {
        var eqLogic = _eqLogic;
    }
    if (isset(eqLogic.configuration[_name])) {
        if (eqLogic.configuration[_name] !== undefined) {
            var trm = '<tr>';
            trm += '	<td class="col-sm-4">';
            trm += '		<span style="font-size : 1em;">' + _label + '</span>';
            trm += '	</td>';
            trm += '	<td>';
            trm += '		<span class="label label-default" style="font-size:1em;white-space:unset !important">';
            trm += '			<span class="eqLogicAttr" data-l1key="configuration" data-l2key="' + _name + '">';
            trm += '			</span>';
            trm += '		</span>';
            trm += '	</td>';
            trm += '</tr>';
            $('#table_infoseqlogic tbody').append(trm);
            $('#table_infoseqlogic tbody tr:last').setValues(eqLogic, '.eqLogicAttr');
        }
    }
}
