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
		<legend><i class="fa fa-cog"></i> {{Gestion}}
			<sup>
				<i class="fa fa-question-circle tooltips" title="{{Pour utiliser le mode inclusion, il faut au préalable avoir activé la fonction AMX Device Discovery sur votre vidéoprojecteur.}}"></i>
			</sup></legend>
		<div class="eqLogicThumbnailContainer">
<style>
.eqLogicThumbnailDisplay .eqLogicThumbnailContainer .changeIncludeState[data-state="0"] {
    background-color : #8000FF !important;
    height : 140px;
    margin-bottom : 10px;
    padding : 5px;
    border-radius: 2px;
    width : 160px;
    margin-left : 10px;
}

.eqLogicThumbnailDisplay .eqLogicThumbnailContainer .fas.fa-sign-in-alt.fa-rotate-90 {
    font-size: 38px !important;
    color: #ea1b39;
}

.eqLogicThumbnailDisplay .eqLogicThumbnailContainer .changeIncludeState > span {
    color: #ea1b39;
}

.eqLogicDisplayCard.cursor {
    height: 180px !important;
    text-align: center;
    background-color: rgb(255, 255, 255);
    margin-bottom: 10px;
    padding: 5px;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px;
    border-bottom-left-radius: 2px;
    width: 160px;
    margin-left: 10px;
    position: absolute;
    left: 0px;
    top: 0px;
}


</style>
      <?php
      if ($state == 1) {
          echo '<div class="cursor changeIncludeState card" data-state="0" >';
          echo '<center>';
          echo '<i class="fas fa-sign-in-alt fa-rotate-90"></i>';
          echo '</center>';
          echo '<span><center>{{Arrêter inclusion}}</center></span>';
          echo '</div>';
      } else {
          echo '<div class="cursor changeIncludeState card" data-state="1" ';
          echo '<center>';
          echo '<i class="fas fa-sign-in-alt fa-rotate-90"></i>';
          echo '</center>';
          echo '<span title="{{Recherche automatique du vidéoprojecteur grâce à la fonction AMX Device Discovery.}}"><center>{{Mode inclusion}}</center></span>';
          echo '</div>';
      }
      ?>
        <div class="cursor eqLogicAction logoSecondary" data-action="add">
				<i class="fa fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fa fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
			<div class="cursor logoSecondary" id="bt_healthoptoma">
				<i class="fas fa-medkit"></i>
				<span><center>{{Santé}}</center></span>
			</div>
        		<div class="cursor logoSecondary" id="bt_documentationOptoma" data-location="<?=$plugin->getDocumentation()?>">
			<i class="icon loisir-livres"></i>
			<br><br>
			<span>{{Documentation}}</span>
		</div>
		</div>
		<legend><i class="fa fa-table"></i> {{Mes Vidéoprojecteurs}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                    $model = (empty($eqLogic->getConfiguration('model'))) ? 'Sans modèle' : $eqLogic->getConfiguration('model');
                    $IP = (empty($eqLogic->getConfiguration('IP'))) ? 'Aucune IP' : $eqLogic->getConfiguration('IP');
                    if (is_object($eqLogic->getCmd('info', 'Powerstatus'))) {
                        $status = ($eqLogic->getCmd('info', 'Powerstatus')->execCmd() == 1) ? "{{Allumé}}" : "{{Éteint}}";
                      $status = "État : " . $status;
                    } else {
                        $status = "";
                    }
                    echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
                    echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95"
                         title="Nom : '.$eqLogic->getName().'</br>
                         Modèle : '.$model.'</br>
                         IP : '.$IP.'</br>
                         '.$status.'">';
                    echo "<br>";
                    echo '<span style="font-size : 14px;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
                    echo '</div>';
                }
            ?>
		</div>
	</div>

	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
				<a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
				<a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Équipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
			<li role="presentation"><a href="#infotab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-info-circle"></i> {{Informations}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<div class="col-xs-6">
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
                                        $options = '';
                                        foreach ((jeeObject::buildTree(null, false)) as $object) {
                                            $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration("parentNumber")) . $object->getName() . '</option>';
                                        }
                                        echo $options;
                                    ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
							<div class="col-sm-9">
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
						<legend>{{Paramètres du vidéoprojecteur}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Entrez tous les paramètres demandés.
Ils ne sont à saisir qu'une seule fois.}}" style="font-size : 1em;color:grey;"></i>
									</sup>
									</legend>
						<fieldset>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Adresse IP}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Entrez l'adresse IP de votre vidéoprojecteur.
Si vous n'avez pas activé la fonction AMX Device Discovery,
saisissez l'adresse manuellement.}}"></i>
									</sup>
							</label>
							<div class="col-sm-5">
								<input id="idipoptoma" type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="IP" placeholder="192.168.X.XXX"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Adresse API}}
									<sup>
										<i class="fa fa-question-circle tooltips" title="{{Sélectionnez l'adresse de l'API de votre vidéoprojecteur.}}"></i>
									</sup>
							</label>
							<div class="col-sm-5">
							    <select id="idipoptoma" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="API">
									<option value="">{{Aucun}}</option>
									<option value="/form/control_cgi">/form/control_cgi</option>
									<option value="/tgi/control.tgi">/tgi/control.tgi ( )</option>
                                </select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label">{{Accès à la page web}}</label>
							<div class="col-sm-3">
								<a class="btn btn-default  pull-left" id="bt_weboptoma"><i class="fa fa-cogs"></i>  {{Interface web Optoma}}</a>
							</div>
						</div>
						<div class="form-group">
 							<label class="col-sm-3 control-label">{{Identifiant et mot de passe}}
 									<sup>
 										<i class="fa fa-question-circle tooltips" title="{{Entrez l'identifiant de votre vidéoprojecteur.
 Ce champ est grisé sur certains vidéoprojecteurs.
 Par défaut : admin}}"></i>
 									</sup>
 									</label>

							<div class="col-sm-3">
 								<input type="text" class="eqLogicAttr form-control" disabled data-l1key="configuration" data-l2key="username" placeholder="admin"/>
 							</div>
                                      
							<div class="col-sm-3">
 								<input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password" placeholder="******"/>
 							</div>
						</fieldset>
					</form>
				</div>
				<div class="form-group" style="display:none">
					<label class="col-sm-3 control-label help">{{Widget équipement}} <sup><i class="fa fa-question-circle tooltips" title="{{Cocher la case pour utiliser le widget associé au type de l'appareil}}"></i>
							</sup></label>
					<div class="col-sm-3">
						<input type="checkbox" class="eqLogicAttr form-control" id="widgetTemplate" data-l1key="configuration" data-l2key="widgetTemplate"/>
					</div>
				</div>
		<div class="col-sm-6">
            <form class="form-horizontal">
                <fieldset>
					<div class="form-group">
						<label class="col-sm-2 control-label">{{Configuration}}</label>
						<div class="col-sm-8">
							<a id="bt_autoDetectModule" class="btn btn-warning" title="{{Recréer les commandes}}" style="display:none"><i class="fas fa-search"></i> {{Recréer les commandes}}</a>
							<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="fileconf" style="display:none"></select>
						</div>
					</div>
				<label class="col-sm-2 control-label">{{Informations}}</label>
            <table id="table_infoseqlogic" class="col-sm-9 table-bordered table-condensed" style="border-radius: 10px;">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
				</br>
					<div class="form-group">
						<div class="col-sm-10">
							<center>
							<img src="core/img/no_image.gif" data-original=".svg" id="img_device" class="img-responsive" style="max-height:450px;min-height:300px;max-width:400px" onerror="this.src='plugins/Optoma/core/config/devices/UHD.png'"/>
							</center>
						</div>
					</div>
					<div class="form-group" style="display:none">
						<label class="col-sm-2 control-label">{{Paramètres}}</label>
							<div class="col-sm-10">
								<a class="btn btn-primary" id="bt_configureDevice" title='{{Configurer}}'><i class="fas fa-wrench"></i> {{Configuration}}</a>
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
							<th style="width: 300px;">{{Nom}}</th>
							<th style="width: 220px;">{{Type}}</th>
							<th>{{Commande information à mettre à jour}}</th>
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