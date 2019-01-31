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

/* * ***************************Includes********************************* */


require_once __DIR__  . '/../../../../core/php/core.inc.php';

class Optoma extends eqLogic {
    /*     * *************************Attributs****************************** */
	const LAMP_MAX = 10000;


    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
		$this->setCategory('multimedia', 1);      
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
        
    }

    public function preUpdate() {
		if (empty($this->getConfiguration('AdrIP'))) {
			throw new Exception(__('L\'adresse IP ne peut pas être vide',__FILE__));
		}

		if (empty($this->getConfiguration('UserId'))) {
			throw new Exception(__('L\'utilisateur ne peut être vide',__FILE__));
		}
    }

    public function postUpdate() {
		if ( $this->getIsEnable() ){
			log::add('Optoma', 'debug', 'Création des commandes dans le postUpdate');

			// Information Power On/Off 
			$info = $this->getCmd(null, 'Power');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Power');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Power');
				$info->setType('info');
				$info->setOrder(2);
				$info->setSubType('binary');
				$info->save();
			}

			// Information Mise sous tension (On) 
			$cmd = $this->getCmd(null, 'On');
			if ( ! is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('On');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('On');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setIsVisible(1);
				$cmd->setOrder(0);
				$cmd->setValue($info->getId());
				$cmd->setTemplate('dashboard', 'PowerOnOff');
				$cmd->setDisplay('parameters',array ( "color" => "green", "type" => "off", "size" =>30 ));
				$cmd->setDisplay('showNameOndashboard','0');
				$cmd->setDisplay('showNameOnplan','0');
				$cmd->setDisplay('showNameOnview','0');
				$cmd->save();
			}

			// Information Mise hors tension (Off) 
			$cmd = $this->getCmd(null, 'Off');
			if ( ! is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('Off');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('Off');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setIsVisible(1);
				$cmd->setOrder(1);
				$cmd->setValue($info->getId());
				$cmd->setTemplate('dashboard', 'PowerOnOff');
				$cmd->setDisplay('parameters',array ( "color" => "green", "type" => "off", "size" =>30 ));
				$cmd->setDisplay('showNameOndashboard','0');
				$cmd->setDisplay('showNameOnplan','0');
				$cmd->setDisplay('showNameOnview','0');
				$cmd->save();
			}

			// Information Model 
			$info = $this->getCmd(null, 'Model');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Model');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Model');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(3);
				$info->setDisplay('showNameOndashboard','0');
				$info->setDisplay('showNameOnplan','0');
				$info->setDisplay('showNameOnview','0');
				$info->save();
			}

			// Information Firmware 
			$info = $this->getCmd(null, 'Firmware');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Firmware');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Firmware');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(4);
				$info->save();
			}

			// Information LANVersion 
			$info = $this->getCmd(null, 'LANVersion');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('LANVersion');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('LANVersion');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(5);
				$info->save();
			}

			// Information IPAddress 
			$info = $this->getCmd(null, 'IPAddress');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('IPAddress');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('IPAddress');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(6);
				$info->save();
			}
			
			// Information SubnetMask 
			$info = $this->getCmd(null, 'SubnetMask');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('SubnetMask');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('SubnetMask');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(7);
				$info->save();
			}

			// Information MACAddress 
			$info = $this->getCmd(null, 'MACAddress');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('MACAddress');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('MACAddress');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(8);
				$info->save();
			}
			
			// Information Refresh 
			$cmd = $this->getCmd(null, 'Refresh');
			if ( ! is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('Refresh');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('Refresh');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setOrder(9);
				$cmd->save();
			}
		}
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */
	

    /*     * **********************Getteur Setteur*************************** */
	public function login() {

		log::add('Optoma', 'debug', 'Tentative d\'authentification.');

		$URL_form_login = 'http://' . $this->getConfiguration('AdrIP') . '/login.asp';
		$URL_action_login = 'http://' . $this->getConfiguration('AdrIP') . '/Info.asp';
		
		for ($login_attemps = 1; $login_attemps <= 5; $login_attemps++) {

			log::add('Optoma', 'debug', 'Connexion au vidéoprojecteur : Tentative '.$login_attemps.'/5.');
			$postValues = array( 'login' => $this->getConfiguration('UserId'),'password' => $this->getConfiguration('MdP') );

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $URL_action_login);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
			$content = curl_exec($curl);
			curl_close($curl);
      
			log::add('Optoma', 'debug', 'content = ' . $content );
          
          	if ( strpos($content, "formInfo") !== FALSE ) {  // si  loggué
			log::add('Optoma', 'debug', 'Authentification réussie.' );
            Return (TRUE);
			} else {        // si non loggué
			log::add('Optoma', 'debug', 'Echec d\'authentification.' );
            Return (FALSE);
			}
		}
		log::add('Optoma', 'error', 'Erreur de connexion au vidéoprojecteur');
		Return (FALSE);
}
	
    public function call_vdp( $cmd ) {
		
		static $VPcookies; 
		static $VPdata = array(
			Model => "",
			Firmware => "",
			LANVersion => "",
			IPAddress => "",
			SubnetMask => "",
			MACAddress => "",
		);

        $URL_form_login = 'http://' . $this->getConfiguration('AdrIP') . '/login.asp';
		$URL_action_login = 'http://' . $this->getConfiguration('AdrIP') . '/Info.asp';
        $URL_control = 'http://' . $this->getConfiguration('AdrIP') . '/control.asp';
      
		$VPcookies = $this->login();
		if ( $VPcookies == FALSE ) // teste si authentification OK
			return;
      
		switch ($cmd){
		case 'On':
			log::add('Optoma', 'debug', 'Commande Refresh - '.$VPcookies[0]);
			$postValues = array( 'login' => $this->getConfiguration('UserId'),'password' => $this->getConfiguration('MdP') );

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $URL_control);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
			$content = curl_exec($curl);
			curl_close($curl);
			
			log::add('Optoma', 'debug', 'control.asp (btn_powon=Power On) = ' . $content );
			$this->checkAndUpdateCmd('Power', 1);
			break;
		
		
		case 'Off':

			log::add('Optoma', 'debug', 'Commande Refresh - '.$VPcookies[0]);
			$postValues = array( 'login' => $this->getConfiguration('UserId'),'password' => $this->getConfiguration('MdP') );

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $URL_control);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
			$content = curl_exec($curl);
			curl_close($curl);
			
			log::add('Optoma', 'debug', 'control.tgi (pwr=off) = ' . $content );
			$this->checkAndUpdateCmd('Power', 0);
			break;
		
		
		case 'Refresh':
		

			log::add('Optoma', 'debug', 'Commande Refresh - '.$VPcookies[0]);
			$postValues = array( 'login' => $this->getConfiguration('UserId'),'password' => $this->getConfiguration('MdP') );

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $URL_action_login);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
			$content = curl_exec($curl);
			curl_close($curl);

			log::add('Optoma', 'debug', 'Decodage Info.asp = ' . $content );
			$VPdata = $this->DecodeEtat($content);
		default:
			$this->checkAndUpdateCmd('Model', $VPdata[Model]);
			$this->checkAndUpdateCmd('Firmware', $VPdata[Firmware]);
			$this->checkAndUpdateCmd('LANVersion', $VPdata[LANVersion]);
			$this->checkAndUpdateCmd('IPAddress', $VPdata[IPAddress]);
			$this->checkAndUpdateCmd('SubnetMask', $VPdata[SubnetMask]);
			$this->checkAndUpdateCmd('MACAddress', $VPdata[MACAddress]);
		}
		$cmd = $this->getCmd(null, 'IPAddress');
		$cmd->setConfiguration('minValue', 0 );
		$cmd->setConfiguration('maxValue', self::LAMP_MAX );
		$cmd->save();
	} 

	public function DecodeEtat($content) {
		$deb = strpos($content, 'Model Name');
		$fin = strpos($content, '</th>', $deb);
		$data[Model] = substr($content, $deb+31, $fin-$deb);

		$deb = strpos($content, 'Firmware Version');
		$fin = strpos($content, '</td>', $deb);
		$data[Firmware] = substr($content, $deb+36, $fin-$deb-13);

		$deb = strpos($content, 'LAN Version');
		$fin = strpos($content, '</td></tr>', $deb);
		$data[LANVersion] = substr($content, $deb+31, $fin-$deb-31);

		$deb = strpos($content, 'IP Address');
		$fin = strpos($content, '</td></tr>', $deb);
		$data[IPAddress] = substr($content, $deb+30, $fin-$deb-30);

		$deb = strpos($content, 'Subnet Mask');
		$fin = strpos($content, '</td></tr>', $deb);
		$data[SubnetMask] = substr($content, $deb+31, $fin-$deb-31);

		$deb = strpos($content, 'MAC Address');
		$fin = strpos($content, '</td></tr>', $deb);
		$data[MACAddress] = substr($content, $deb+31, $fin-$deb-31);

		log::add('Optoma', 'debug', 'Model=' . $data[Model] . ' Firmware=' . $data[Firmware] . ' LANVersion=' . $data[LANVersion] . ' IPAddress=' . $data[IPAddress] . ' SubnetMask=' . $data[SubnetMask] . ' MACAddress=' . $data[MACAddress]);
		
		return ($data);
	}
}

class OptomaCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
		
		$eqLogic = $this->getEqLogic(); //récupère l'éqlogic de l'équipement
		
		switch ($this->getLogicalId()) {	
		case 'On':
			log::add('Optoma', 'debug', 'Exécution de la commande On');
			$eqLogic->call_vdp ('On');
			break;
		
		case 'Off':
			log::add('Optoma', 'debug', 'Exécution de la commande Off');
			$eqLogic->call_vdp ('Off');
			break;
		
		default:
			log::add('Optoma', 'debug', 'exécution de la commande Refresh');
			$eqLogic->call_vdp ('Refresh');
		}
	}
}


