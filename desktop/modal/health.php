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

if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
$eqLogics = Optoma::byType('Optoma');
?>

<table class="table table-condensed tablesorter" id="table_healthoptoma">
	<thead>
		<tr>
			<th>{{Appareil}}</th>
			<!--th>{{ID}}</th!-->
			<th>{{IP}}</th>
            <th>{{Modèle}}</th>
			<th>{{Statut}}</th>
			<th>{{Allumé ?}}</th>
			<th>{{Versions}}</th>
			<th>{{Dernière communication}}</th>
			<th>{{Date création}}</th>
		</tr>
	</thead>
	<tbody>
      <?php
        foreach ($eqLogics as $eqLogic) {
          echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';

          //echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getId() . '</span></td>';

          echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('IP') . '</span></td>';

          echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('type') . " " . $eqLogic->getConfiguration('model') . '</span></td>';

          $status = '<span class="label label-success" style="font-size : 1em; cursor : default;">{{OK}}</span>';
          if ($eqLogic->getStatus('state') == 'nok') {
              $status = '<span class="label label-danger" style="font-size : 1em; cursor : default;">{{NOK}}</span>';
          }
          echo '<td>' . $status . '</td>';
          $powerstatus = $eqLogic->getCmd('info', 'Powerstatus');
          if (is_object($powerstatus)) {
              $powervalue = $powerstatus->execCmd();
          }
          if ($powervalue == 1) {
              $power = '<span class="label label-success" style="font-size : 1em;" title="{{Allumé}}"><i class="icon maison-cinema1"></i></span>';
          } else {
              $power = '<span class="label label-danger" style="font-size : 1em;" title="{{Éteint}}"><i class="fa fa-times"></i></span>';
          }
          echo '<td>' . $power . '</td>';

          $RS232Version = $eqLogic->getConfiguration('RS232Version');
          $SoftwareVersion = $eqLogic->getConfiguration('SoftwareVersion');
          $LANFirmwareVersion = $eqLogic->getConfiguration('LANFirmwareVersion');
          echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $LANFirmwareVersion . ' - ' . $RS232Version . ' - ' . $SoftwareVersion . '</span></td>';

          echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getStatus('lastCommunication') . '</span></td>';
          echo '<td><span class="label label-info" style="font-size : 1em; cursor : default;">' . $eqLogic->getConfiguration('createtime') . '</span></td></tr>';
        }
      ?>
	</tbody>
</table>
