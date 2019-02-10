<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('Optoma');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un Vidéoprojecteur}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
        <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
		   </ul>
	   </div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
        <div class="cursor eqLogicAction" data-action="add" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<i class="fa fa-plus-circle" style="font-size : 6em;color:#02cac3;"></i>
				<br>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#02cac3">{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
				<br>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
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
							<label class="col-sm-3 control-label">{{Nom du vidéoprojecteur}}</label>
							<div class="col-sm-5">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du vidéoprojecteur}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-5">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (object::all() as $object) {
										echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
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
							<div class="col-sm-9">
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
Si la fonction AMX Device Discovery est activée,
cliquez sur le bouton « AMX Device Discovery ».
La recherche peut durer jusqu'à 1 minute." style="font-size : 1.5em;color:grey;"></i>
									</sup>
							</label>
							<div class="col-sm-3">
								<input id="idipoptoma" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="AdrIP" placeholder="192.168.1.30"/>
							</div>
							<a class="btn btn-warning  pull-left" id="bt_amxDeviceDiscovery"><i class="fa fa-search"></i>  {{AMX Device Discovery}}</a>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Accès à la page web}}</label>
							<div class="col-sm-3">
								<a class="btn btn-default  pull-left" id="bt_weboptoma"><i class="fa fa-cogs"></i>  {{Interface web Optoma}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Identifiant}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="Entrez l'identifiant de votre vidéoprojecteur.
Ce champ est grisé sur certains vidéoprojecteurs.
Par défaut : admin" style="font-size : 1.5em;color:grey;"></i>
									</sup>
									</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="UserId" placeholder="admin"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Mot de passe}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Entrez le mot de passe de votre vidéoprojecteur.
Par défaut : admin" style="font-size : 1.5em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-sm-3">
								<input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="MdP" placeholder="admin"/>
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
                <legend>Configuration du plugin</legend>
				<div class="col-sm-7">
					<form class="form-horizontal">
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
                                    <label class="radio-inline"><input type="radio" name="config" class="eqLogicAttr" data-l1key="configuration" data-l2key="askWebParsing" />{{HTTP parse}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Nécessite l'activation du HTTP sur le vidéoprojecteur.
(récupération des informations uniquement)" style="font-size : 1.5em;color:grey;"></i>
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
						</div>
							<div class="form-group">
								<label class="col-sm-6 control-label" >{{Intervalle de rafraîchissement des informations}}
								<sup>
										<i class="fa fa-question-circle tooltips" title="Envoi de la commande 'Refresh' à intervale régulier" style="font-size : 1.5em;color:grey;"></i>
								</sup>
                                  </label>
								<div class="col-sm-4 input-group">
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
							</div>
                </fieldset>
					</form>
				</div>  
        	</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th><th>{{Type}}</th><th>{{Valeur}}</th><th>{{Paramètres}}</th><th>{{Action}}</th>
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
							<th>{{Nom}}</th><th>{{Type}}</th><th>{{Valeur}}</th><th>{{Paramètres}}</th><th>{{Action}}</th>
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


