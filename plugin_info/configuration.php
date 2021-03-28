
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
   if (!isConnect()) {
       include_file('desktop', '404', 'php');
       die();
   }
   $plugin = plugin::byId('Optoma');
   sendVarToJS('version', Optoma::$_pluginVersion);

   ?>
<form class="form-horizontal">
   <fieldset>
      <legend>
         <i class="fa fa-list-alt"></i> {{Général}}
      </legend>
      <div class="form-group">
         <?php
            $update = $plugin->getUpdate();
            if (is_object($update)) {
                echo '<div class="col-lg-3">';
                echo '<div>';
                echo '<label>{{Branche}} :</label> '. $update->getConfiguration('version', 'stable');
                echo '</div>';
                echo '<div>';
                echo '<label>{{Source}} :</label> ' . $update->getSource();
                echo '</div>';
                echo '<div>';
                echo '<label>{{Version}} :</label> v' . ((Optoma::$_pluginVersion)?Optoma::$_pluginVersion:' '). ' (' . $update->getLocalVersion() . ')';
                echo '</div>';
                echo '</div>';
            }
            ?>
         <div class="col-lg-5">
            <div>
               <i><a class="btn btn-primary btn-xs" target="_blank" href="https://flobul-domotique.fr/presentation-du-plugin-Optoma-pour-jeedom/"><i class="fas fa-book"></i><strong> {{Présentation du plugin}}</strong></a></i>
               <i><a class="btn btn-success btn-xs" target="_blank" href="<?=$plugin->getDocumentation()?>"><i class="fas fa-book"></i><strong> {{Documentation complète du plugin}}</strong></a></i>
            </div>
            <div>
               <i> {{Les dernières actualités du plugin}}<a class="btn btn-label btn-xs" target="_blank" href="https://community.jeedom.com/t/plugin-Optoma-documentation-et-actualites/39994"><i class="icon jeedomapp-home-jeedom icon-Optoma"></i><strong>{{sur le community}}</strong></a>.</i>
            </div>
            <div>
               <i> {{Les dernières discussions autour du plugin}}<a class="btn btn-label btn-xs" target="_blank" href="https://community.jeedom.com/tags/plugin-Optoma"><i class="icon jeedomapp-home-jeedom icon-Optoma"></i><strong>{{sur le community}}</strong></a>.</i></br>
               <i> {{Pensez à mettre le tag}} <b><font font-weight="bold" size="+1">#plugin-Optoma</font></b> {{et à fournir les log dans les balises préformatées}}.</i>
            </div>
            <style>
               .icon-Optoma {
               font-size: 1.3em;
               color: #94CA02;
               }
            </style>
         </div>
      </div>
      <legend>
         <i class="fas fa-cogs"></i> {{Paramètres}}
      </legend>
      <div class="form-group">
         <label class="col-lg-4 control-label"><strong> {{Intervalle de rafraîchissement des informations (cron)}}</strong>
             <sup><i class="fas fa-question-circle" title="{{Sélectionnez l'intervalle auquel le plugin ira récupérer les informations sur le vidéoprojecteur.</br>Par défaut : 1 minute.}}"></i></sup>
         </label>
         <div class="col-lg-4">
            <select class="configKey form-control" data-l1key="autorefresh" >
               <option value="* * * * *">{{Toutes les minutes}}</option>
               <option value="*/2 * * * *">{{Toutes les 2 minutes}}</option>
               <option value="*/3 * * * *">{{Toutes les 3 minutes}}</option>
               <option value="*/4 * * * *">{{Toutes les 4 minutes}}</option>
               <option value="*/5 * * * *">{{Toutes les 5 minutes}}</option>
               <option value="*/10 * * * *">{{Toutes les 10 minutes}}</option>
               <option value="*/15 * * * *">{{Toutes les 15 minutes}}</option>
               <option value="*/30 * * * *">{{Toutes les 30 minutes}}</option>
               <option value="*/45 * * * *">{{Toutes les 45 minutes}}</option>
               <option value="">{{Jamais}}</option>
            </select>
         </div>
      </div>

      <div class="form-group">
         <legend>
            <i class="icon loisir-darth"></i> {{Configuration optionnelle du démon}}
         </legend>
        <div class="form-group">
          <label class="col-sm-4 control-label"><strong> {{Adresse IP du vidéoprojecteur pour le démon}}</strong>
              <sup><i class="fas fa-question-circle" title="{{Entrez l'adresse IP du vidéoprojecteur.</br>Elle peut être identique à un équipement.}}"></i></sup>
          </label>
          <div class="col-sm-2">
              <input type="text" class="configKey form-control deviceir" data-l1key="ip" ><br>
          </div>
      </div>
        <div class="form-group">
         <label class="col-sm-4 control-label"> {{Numéro du port d'écoute Telnet}}
             <sup><i class="fas fa-question-circle" title="{{Uniquement le port 23.}}</br><strong>Attention,</strong> {{c'est le seul port sur lequel les trames INFO sont envoyées par le vidéoprojecteur.}}</br>{{(Les ports 1023 et 2023 vont connecter le démon, mais aucun message ne remontera).}}</br>{{Le démon ne sert qu'à récupérer les trames emises automatiquement par le vidéoprojecteur.}}"></i></sup>
         </label>
         <div class="col-sm-2">
            <input type="text" class="configKey form-control " data-l1key="listenport" ><br>
         </div>
      </div>
      </div>
   </fieldset>
</form>
<script>
   var dateVersion = $("#span_plugin_install_date").html();
   $("#span_plugin_install_date").empty().append("v" + version + " (" + dateVersion + ")");
</script>
