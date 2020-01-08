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


$('.eqLogicAttr[data-l2key=askCGI]').on('click',function(){
  $('#option_cgi').css({visibility: "visible"});
});

$('.eqLogicAttr[data-l2key=askTelnet]').on('click',function(){
  $('#option_cgi').css({visibility: "hidden"});
});

$('.eqLogicAttr[data-l2key=askPJLink]').on('click',function(){
  $('#option_cgi').css({visibility: "hidden"});
});

$('#bt_weboptoma').on('click', function () {
  var nodeId = $('#idipoptoma').value();
  $('#md_modal').dialog({title: "{{Interface Optoma}}"});
  $('#md_modal').load('index.php?v=d&plugin=Optoma&modal=web&ip=' + nodeId).dialog('open');
});

$('#bt_searchCGILink').on('click', function () {
	searchCGILink();
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
			if ($('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value() != '') {
				bootbox.confirm('{{Voulez-vous écraser l\'adresse IP actuelle ?}}', function (result) {
				if (result) {
					$('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value(data.result);
				}
				});
			}
			else {
				$('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value(data.result);
			}
		}
	});
}

function webOptoma() {
  		if ($('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value() != '') {
			$('#div_alert').showAlert({message: '{{Recherche du lien CGI en cours. (environ 15 secondes)}}', level: 'warning'});
			$url = $('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value();
            $.ajax({
                type: "POST",
                url: "plugins/Optoma/core/ajax/Optoma.ajax.php",
                data: {
                    action: "webOptoma",
                },
                dataType: 'json',
                global: false,
                error: function (request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function (data) {
                   }
            });
        }
}

function searchCGILink() {
		$AdrIP = $('.eqLogicAttr[data-l1key=configuration][data-l2key=AdrIP]').value();
  		$CGILink = $('.eqLogicAttr[data-l1key=configuration][data-l2key=ControlCGI]').value();

  		if ($AdrIP != ''){
			$('#div_alert').showAlert({message: '{{Recherche du lien CGI en cours. (environ 15 secondes)}}', level: 'warning'});
            $.ajax({
                type: "POST",
                url: "plugins/Optoma/core/ajax/Optoma.ajax.php",
                data: {
                    action: "searchCGILink",
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
					if ($CGILink != '' && $CGILink != data.result) {
                         bootbox.confirm('{{Voulez-vous écraser le lien actuel ?}}', function (result) {
                               if (result) {
                                    $('.eqLogicAttr[data-l1key=configuration][data-l2key=ControlCGI]').value(data.result);
                                    $('#div_alert').showAlert({message: 'Lien CGI écrasé ! Veuillez sauvegarder.', level: 'success'});
                               }
                          });
                    }
					else if ($CGILink == data.result) {
						$('#div_alert').showAlert({message: '{{Lien identique.}}', level: 'warning'});
                    }
                  	else if ($CGILink == '') {
                        $('.eqLogicAttr[data-l1key=configuration][data-l2key=ControlCGI]').value(data.result);
                        $('#div_alert').showAlert({message: 'Lien CGI trouvé ! Veuillez sauvegarder.', level: 'success'});
                    }
					else {
                      $('#div_alert').showAlert({message: data.result, level: 'danger'});
                      return;
                    }
                }
            });
        }
  		else {
          $('#div_alert').showAlert({message: 'Merci de remplir l\'adresse IP.', level: 'danger'});
        }
}

/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.Optoma
 */

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
		tr += '<td>';
		tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}} style="margin-top : 5px" >';
		tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="margin-top : 5px;margin-right : 10px;" title="La valeur de la commande vaut par défaut la commande" >';
		tr += '<option value="">Aucune</option>';
		tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-top : -5px;width : 40%; display : inline-block;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="margin-top : -5px;width : 50%; display : inline-block;" ></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input readonly class="cmdAttr" id="'+ _cmd.id +'value" style="width : 200px; font-style: italic; ">';
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
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="Min" title="Min" style="width : 30%; display : inline-block;margin-top : 5px;margin-right : 5px;" >';
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="Max" title="Max" style="width : 30%; display : inline-block;margin-top : 5px;margin-right : 5px;" >';
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
		tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="margin-top : 0;width : 50%; display : inline-block;" placeholder="{{Nom}}" >';
		tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="margin-top : 0;width : 50%; display : inline-block;" title="La valeur de la commande vaut par défaut la commande">';
		tr += '<option value="">Aucune</option>';
		tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-top : -5px;width : 40%; display : inline-block;" />';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '" style="margin-top : -5px;width : 50%; display : inline-block;" ></span>';
		tr += '</td>';
		tr += '<td>';
		tr += '</td>';
		tr += '<td>';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span> ';
		tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
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
			id: $(".li_eqLogic.active").attr('data-eqLogic_id'),
			filter: {type: 'info'},
			error: function (error) {
				$('#div_alert').showAlert({message: error.message, level: 'danger'});
			},
			success: function (result) {
				tr.find('.cmdAttr[data-l1key=value]').append(result);
				tr.find('.cmdAttr[data-l1key=configuration][data-l2key=value]').append(result);
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
				$('.changeIncludeState:not(.card)').html('<i class="fa fa-sign-in fa-rotate-90"></i> {{Arreter inclusion}}');
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
