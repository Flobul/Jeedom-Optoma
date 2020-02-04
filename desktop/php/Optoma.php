<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('Optoma');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
$state = config::byKey('include_mode', 'Optoma', 0);
echo '<div id="div_inclusionAlert"></div>';
if ($state == 1) {
  echo '<div class="alert jqAlert alert-warning" id="div_inclusionAlert" style="margin : 0px 5px 15px 15px; padding : 7px 35px 7px 15px;">{{Vous êtes en mode inclusion. Cliquez à nouveau sur le bouton d\'inclusion pour sortir de ce mode}}</div>';
}
?>
<div class="row row-overflow">
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend><i class="fa fa-cog"></i>  {{Gestion}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Pour utiliser le mode inclusion, il faut au préalable avoir activé la fonction AMX Device Discovery sur votre vidéoprojecteur."
                                      style="font-size : 1em;color:grey;"></i>
									</sup></legend>
		<div class="eqLogicThumbnailContainer">
                <?php
      if ($state == 1) {
        echo '<div class="cursor changeIncludeState card" data-state="0" style="background-color : #8000FF; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
        echo '<center>';
        echo '<i class="fas fa-sign-in-alt fa-rotate-90" style="font-size : 3em;color:#ea1b39;"></i>';
        echo '</center>';
        echo '<span style="font-size : 1.1em;position:relative; top : 11px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#ea1b39"><center>{{Arrêter inclusion}}</center></span>';
        echo '</div>';
      } else {
        echo '<div class="cursor changeIncludeState card" data-state="1" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
        echo '<center>';
        echo '<i class="fas fa-sign-in-alt fa-rotate-90" style="font-size : 3em;color:#ea1b39;"></i>';
        echo '</center>';
        echo '<span style="font-size : 1.1em;position:relative; top : 11px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#ea1b39"><center>{{Mode inclusion}}</center></span>';
        echo '</div>';
      }
      ?>
        <div class="cursor eqLogicAction" data-action="add" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<i class="fa fa-plus-circle" style="font-size : 6em;color:#ea1b39;"></i>
				<br>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#ea1b39">{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
				<br>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
            		</div>
            	<div class="cursor" id="bt_healthoptoma" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
              <center>
                <i class="fas fa-medkit" style="font-size : 3em;color:#767676;"></i>
              </center>
              <span style="font-size : 1.1em;position:relative; top : 11px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Santé}}</center></span>
            </div>
		</div>
		<legend><i class="fa fa-table"></i> {{Mes VidéoProjecteurs}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				foreach ($eqLogics as $eqLogic) {
					$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
					echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
					echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
					echo "<br>";
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
					echo '</div>';
				}
			?>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
		<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
		<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
			<li role="presentation"><a href="#infotab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-info-circle"></i> {{Informations}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<div class="col-sm-5">
					<form class="form-horizontal">
				<fieldset>
						<div class="form-group">
						<legend>Général</legend>
							<label class="col-sm-4 control-label">{{Nom du vidéoprojecteur}}</label>
							<div class="col-sm-5">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du vidéoprojecteur}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" >{{Objet parent}}</label>
							<div class="col-sm-5">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (jeeObject::all() as $object) {
										echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Catégorie}}</label>
							<div class="col-sm-6">
								<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
									}
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-5">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
				</fieldset>
					</form>
				</div>
				<div class="col-sm-6">
					<form class="form-horizontal">
						<legend>Paramètres du vidéoprojecteur
									<sup>
										<i class="fa fa-question-circle tooltips" title="Entrez tous les paramètres demandés.
Ils ne sont à saisir qu'une seule fois." style="font-size : 1em;color:grey;"></i>
									</sup>
									</legend>
						<fieldset>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Adresse IP}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Entrez l'adresse IP de votre vidéoprojecteur.
Si vous n'avez pas activé la fonction AMX Device Discovery,
saisissez l'adresse manuellement." style="font-size : 1.5em;color:grey;"></i>
									</sup>
							</label>
							<div class="col-sm-5">
								<input id="idipoptoma" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="AdrIP" placeholder="192.168.1.30"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Accès à la page web}}</label>
							<div class="col-sm-3">
								<a class="btn btn-default  pull-left" id="bt_weboptoma"><i class="fa fa-cogs"></i>  {{Interface web Optoma}}</a>
							</div>
						</div>
						<div class="form-group" id="option_cgi">
							<label class="col-sm-3 control-label">{{Lien CGI}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Entrez le lien control_cgi de votre vidéoprojecteur.
Il se trouve dans une fonction du fichier javascript de la page web index.asp.
Pour le modèle UHD51 : http://@IP/form/control_cgi
Sinon, cliquer sur le bouton « Recherche CGI »" style="font-size : 1.5em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-sm-5">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ControlCGI" placeholder="http://@IP/*cgi"/>
							</div>
							<a class="btn btn-warning  pull-left" id="bt_searchCGILink"><i class="fa fa-search"></i>  {{Recherche CGI}}</a>
						</div>
						</fieldset>
					</form>
				</div>
				<div class="col-sm-6">
					<form class="form-horizontal">
						<legend>Configuration du plugin
						<sup>
						<i class="fa fa-question-circle tooltips" title="Choisissez une méthode pour la récupération des informations/contrôle,
ainsi qu'un intervalle pour le rafraissement des informations.
Par défaut, la méthode  « Requête CGI » est appliquée,
sans rafraîchissement des données." style="font-size : 1em;color:grey;"></i>
								</sup>
								</legend>
						<fieldset>
						<div class="form-group">
							<label class="col-sm-6 control-label">{{Méthode de récupération des informations et de contrôle du vidéoprojecteur}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Choisissez un protocole de contrôle.
Assurez-vous que le protocole est activé sur votre vidéoprojecteur.
Requête CGI est le mode préférentiel." style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
							<div class="col-sm-6">
                            	<div>
                                    <label class="radio-inline"><input type="radio" name="config" class="eqLogicAttr" data-l1key="configuration" data-l2key="askCGI" />{{Requête CGI (recommandée)}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Nécessite l'activation du HTTP sur le vidéoprojecteur.
(récupération des informations et commandes)" style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
                                </div>
                            	<div>
                                	<label class="radio-inline"><input type="radio" name="config" class="eqLogicAttr" data-l1key="configuration" data-l2key="askTelnet" />{{Telnet}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Nécessite l'activation du Telnet sur le vidéoprojecteur.
(récupération des informations et commandes)" style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
                                </div>
                            	<div>
                                    <label class="radio-inline"><input type="radio" name="config" class="eqLogicAttr" data-l1key="configuration" data-l2key="askPJLink" />{{PJLink}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Nécessite l'activation de PJ Link sur le vidéoprojecteur.
(récupération des informations et commandes)" style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
                                </div>
							</div>
								<label class="col-sm-6 control-label" >{{Intervalle de rafraîchissement des informations}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Récupération des informations par envoi de la commande 'Refresh' à intervalle choisi.
La commande est envoyée toutes les minutes, 5 minutes, 15 minutes, 30 minutes..." style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
								<div class="col-sm-3 input-group">
									<select class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="RepeatCmd">
										<option value="">{{Non}}</option>
										<option value="cron">{{Toutes les minutes}}</option>
										<option value="cron5">{{Toutes les 5 minutes}}</option>
										<option value="cron15">{{Toutes les 15 minutes}}</option>
										<option value="cron30">{{Toutes les 30 minutes}}</option>
										<option value="cronHourly">{{Toutes les heures}}</option>
										<option value="cronDaily">{{Tous les jours}}</option>
									</select>
								</div>
					</fieldset>
						</form>
					</div>
        	</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th style="width: 300px;">{{Nom}}</th>
                            <th style="width: 220px;">{{Type}}</th>
							<th>{{Valeur}}</th>
							<th style="width: 200px;">{{Paramètres}}</th>
							<th style="width: 90px;">{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="infotab">
				<table id="table_info" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th style="width: 190px;">{{Nom}}</th>
							<th style="width: 220px;">{{Type}}</th>
							<th>{{Valeur}}</th>
							<th style="width: 200px;">{{Paramètres}}</th>
							<th style="width: 90px;">{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php include_file('desktop', 'Optoma', 'js', 'Optoma');?>
<?php include_file('core', 'plugin.template', 'js');?>
