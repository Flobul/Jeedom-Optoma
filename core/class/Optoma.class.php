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

include('simple_html_dom.php');
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class Optoma extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */
	public static function cron() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cron"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}
  
	public static function cron5() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cron5"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}
  
  	public static function cron15() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cron15"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}

	public static function cron30() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cron30"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}
  
	public static function cronHourly() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cronHourly"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}

  	public static function cronDaily() {
		foreach(eqLogic::byType('Optoma') as $Optoma){		
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cronDaily"){
                   $cmd = $Optoma->getCmd(null, 'Refresh');
                   if (!is_object($cmd)) {
                     continue;
                   }
                   $cmd->execCmd();
				}
			}
		}
	}
  

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
		$this->setCategory('multimedia', 1);
      	$this->setIsEnable(1);
		$this->setIsVisible(1);
    }

    public function postInsert() {

    }

    public function postSave() {
    	$cmd = $this->getCmd(null, 'Refresh');
    	if (is_object($cmd)) {
    		 $cmd->execCmd();
    	}
    }

    public function preSave() {

    }        
    
    public function preUpdate() {
		if (empty($this->getConfiguration('AdrIP'))) {
			throw new Exception(__('L\'adresse IP ne peut pas être vide',__FILE__));
		}

		if (empty($this->getConfiguration('UserId'))) {
			throw new Exception(__('L\'utilisateur ne peut être vide',__FILE__));
		}
      
		if ($this->getConfiguration('askCGI') == 0 && $this->getConfiguration('askTelnet') == 0 && $this->getConfiguration('askPJLink') == 0 && $this->getConfiguration('askWebParsing') == 0) {
			throw new Exception(__('Le protocole de contrôle ne peut pas être vide',__FILE__));
		}

		if ($this->getConfiguration('askCGI') == 1) {
            if (empty($this->getConfiguration('ControlCGI'))) {
				throw new Exception(__('Le lien CGI ne peut pas être vide',__FILE__));
            }
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$this->getConfiguration('ControlCGI'))) {
			throw new Exception(__('Le format du lien CGI est incorrect',__FILE__));
        	}
		}
    }

    public function postUpdate() {
		if ( $this->getIsEnable() ){
			log::add('Optoma', 'debug', 'Création des commandes dans le postUpdate');
			// Information Power On/Off 
			$info = $this->getCmd(null, 'Powerstatus');
			if (!is_object($info)) {
				$info = new OptomaCmd();
                $info->setName('Powerstatus');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Powerstatus');
                $info->setType('info');
                $info->setSubType('binary');
                $info->setOrder(1);
                $info->setIsVisible(1);
                $info->save();
            }
			// Commande Mise sous tension (On) 
			$cmd = $this->getCmd(null, 'On');
			if (!is_object($cmd)) {
				$cmd = new OptomaCmd();
                $cmd->setName('On');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setLogicalId('On');
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setIsVisible(1);
                $cmd->setOrder(2);
                $cmd->setValue($info->getId());
                $cmd->setTemplate('dashboard', 'PowerOnOff');
                $cmd->setDisplay('parameters',array ( "color" => "green", "type" => "off", "size" =>30 ));
                $cmd->setDisplay('showNameOndashboard','0');
                $cmd->setDisplay('showNameOnplan','0');
                $cmd->setDisplay('showNameOnview','0');
                $cmd->save();
            }
			// Commande Mise hors tension (Off) 
			$cmd = $this->getCmd(null, 'Off');
			if (!is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('Off');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('Off');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setIsVisible(1);
				$cmd->setOrder(3);
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
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Modèle');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Model');
				$info->setType('info');
				$info->setSubType('string');
    		    $info->setOrder(0);
    		    $info->setIsVisible(1);
				$info->save();
            }
			// Information Firmware 
			$info = $this->getCmd(null, 'Firmware');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Version Firmware');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Firmware');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
            }
			// Information LANVersion 
			$info = $this->getCmd(null, 'LANVersion');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Version LAN');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('LANVersion');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
            }
			// Information IPAddress 
			$info = $this->getCmd(null, 'IPAddress');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Adresse IP');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('IPAddress');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
			}
			// Information SubnetMask 
			$info = $this->getCmd(null, 'SubnetMask');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Masque de sous-réseau');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('SubnetMask');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
			}
			// Information MACAddress 
			$info = $this->getCmd(null, 'MACAddress');
			if (!is_object($info)) {
				$info = new OptomaCmd();
			}
				$info->setName('Adresse MAC');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('MACAddress');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
			// Information AV Mute 
			$info = $this->getCmd(null, 'AV Mute');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('AV Mute');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('AV Mute');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
			// Information Freeze
			$info = $this->getCmd(null, 'Freeze');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Freeze');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Freeze');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
          	// Information Information Hide
			$info = $this->getCmd(null, 'Information Hide');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Information Hide');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Information Hide');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
			}
          	// Information High Altitude
			$info = $this->getCmd(null, 'High Altitude');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('High Altitude');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('High Altitude');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
          	// Information Keypad Lock
			$info = $this->getCmd(null, 'Keypad Lock');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Keypad Lock');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Keypad Lock');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
          	// Information Display Mode Lock
			$info = $this->getCmd(null, 'Display Mode Lock');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Display Mode Lock');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Display Mode Lock');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
          	// Information Direct Power On
			$info = $this->getCmd(null, 'Direct Power On');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Direct Power On');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Direct Power On');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
			// Information 3D Sync. Invert
			$info = $this->getCmd(null, '3D Sync Invert');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Sync Invert');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3D Sync Invert');
				$info->setType('info');
				$info->setSubType('binary');
        		$info->setIsVisible(0);
				$info->save();
            }
			// Information 3D Mode
			$info = $this->getCmd(null, '3DModeOnOffStatus');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Mode On Off');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3DModeOnOffStatus');
				$info->setType('info');
				$info->setSubType('binary');
        		$info->setOrder(9);
        		$info->setIsVisible(1);
				$info->save();
            }
			// Information 3D Mode
			$info = $this->getCmd(null, '3D Mode');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Mode');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3D Mode');
				$info->setType('info');
				$info->setOrder(10);
				$info->setSubType('string');
				$info->save();
            }
			// Information 3D-2D
			$info = $this->getCmd(null, '3D-2D');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D-2D');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3D-2D');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
            }
			// Information 3D Format
			$info = $this->getCmd(null, '3D Format');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Format');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3D Format');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(11);
				$info->setIsVisible(1);
				$info->save();
            }
			// Information Internal Speaker
			$info = $this->getCmd(null, 'Internal Speaker');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Internal Speaker');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Internal Speaker');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }        
            // Information Mute
			$info = $this->getCmd(null, 'Mute');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Mute');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Mute');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
			// Information Dynamic Black
			$info = $this->getCmd(null, 'Dynamic Black');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Dynamic Black');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Dynamic Black');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setOrder(8);
        		$info->setIsVisible(1);
				$info->save();
            }
			// Information Volume(Audio)
      		$info = $this->getCmd(null, 'Volume(Audio)');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Volume(Audio)');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Volume(Audio)');
				$info->setType('info');
				$info->setSubType('numeric');
				$info->setIsVisible(0);
				$info->save();
            }
			// Information Volume(Mic)
      		$info = $this->getCmd(null, 'Volume(Mic)');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Volume(Mic)');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Volume(Mic)');
				$info->setType('info');
				$info->setSubType('numeric');
				$info->setIsVisible(0);
				$info->save();
      		}
			// Information Audio Input
      		$info = $this->getCmd(null, 'Audio Input');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Audio Input');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Audio Input');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
      		}
			// Information Source
      		$info = $this->getCmd(null, 'Source');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Source');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Source');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(4);
				$info->setIsVisible(1);
				$info->save();
      		}
      		// Information Brightness
      		$info = $this->getCmd(null, 'Brightness');
      		if ( ! is_object($info)) {
      			$info = new OptomaCmd();
                $info->setName('Brightness');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Brightness');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Contrast
      		$info = $this->getCmd(null, 'Contrast');
      		if ( ! is_object($info)) {
      			$info = new OptomaCmd();
                $info->setName('Contrast');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Contrast');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Sharpness
      		$info = $this->getCmd(null, 'Sharpness');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Sharpness');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Sharpness');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Phase
      		$info = $this->getCmd(null, 'Phase');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Phase');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Phase');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Brilliant Color
      		$info = $this->getCmd(null, 'Brilliant Color');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Brilliant Color');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Brilliant Color');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Gamma
      		$info = $this->getCmd(null, 'Gamma');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Gamma');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Gamma');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Color Temperature
      		$info = $this->getCmd(null, 'Color Temperature');
      		if ( ! is_object($info)) {
      			$info = new OptomaCmd();
                $info->setName('Color Temperature');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Color Temperature');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Display Mode
      		$info = $this->getCmd(null, 'Display Mode');
      		if ( ! is_object($info)) {
                 $info = new OptomaCmd();
                $info->setName('Display Mode');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Display Mode');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(5);
                $info->setIsVisible(1);
                $info->save();
            }
      		// Information Color Space
      		$info = $this->getCmd(null, 'Color Space');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Color Space');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Color Space');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information 12V Trigger
      		$info = $this->getCmd(null, '12V Trigger');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('12V Trigger');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('12V Trigger');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information Aspect Ratio
      		$info = $this->getCmd(null, 'Aspect Ratio');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Aspect Ratio');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Aspect Ratio');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(6);
                $info->setIsVisible(1);
                $info->save();
            }
      		// Information Screen Type
      		$info = $this->getCmd(null, 'Screen Type');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Screen Type');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Screen Type');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Projection
      		$info = $this->getCmd(null, 'Projection');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Projection');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Projection');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Zoom Value
      		$info = $this->getCmd(null, 'Zoom Value');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Zoom Value');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Zoom Value');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information H. Keystone
      		$info = $this->getCmd(null, 'H. Keystone');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('H. Keystone');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('H. Keystone');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information V. Keystone
      		$info = $this->getCmd(null, 'V. Keystone');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('V. Keystone');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('V. Keystone');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information H.Image Shift
      		$info = $this->getCmd(null, 'H.Image Shift');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('H.Image Shift');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('H.Image Shift');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information V.Image Shift
      		$info = $this->getCmd(null, 'V.Image Shift');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('V.Image Shift');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('V.Image Shift');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information Four Corners
      		$info = $this->getCmd(null, 'Four Corners');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Four Corners');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Four Corners');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
      		}
      		// Information Sleep Timer
      		$info = $this->getCmd(null, 'Sleep Timer');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Sleep Timer');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Sleep Timer');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Projector ID
      		$info = $this->getCmd(null, 'Projector ID');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Projector ID');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Projector ID');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
      		// Information Remote Code
      		$info = $this->getCmd(null, 'Remote Code');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Remote Code');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Remote Code');
				$info->setType('info');
				$info->setSubType('numeric');
				$info->setIsVisible(0);
				$info->save();
      		}
      		// Information Background Color
      		$info = $this->getCmd(null, 'Background Color');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Background Color');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Background Color');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
      		}
      		// Information Wall Color
      		$info = $this->getCmd(null, 'Wall Color');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Wall Color');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Wall Color');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
      		}
			// Information Logo
			$info = $this->getCmd(null, 'Logo');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Logo');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Logo');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
      		}
      		// Information Power mode
      		$info = $this->getCmd(null, 'Power mode');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Power mode');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Power mode');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
      		}
      		// Information Brightness mode
      		$info = $this->getCmd(null, 'Brightness Mode');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Brightness Mode');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Brightness Mode');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(7);
                $info->setIsVisible(1);
                $info->save();
      		}
      		// Commande Refresh 
      		$cmd = $this->getCmd(null, 'Refresh');
      		if ( ! is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('Refresh');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('Refresh');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setOrder(49);
        		$cmd->setIsVisible(1);
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

//			log::add('Optoma', 'debug', 'content = ' . $content );

        if ( strpos($content, "formInfo") !== FALSE ) {  // si  loggué
			log::add('Optoma', 'debug', 'Authentification réussie.' );
            Return (TRUE);
			} else {        // si non loggué
			log::add('Optoma', 'debug', 'Echec d\'authentification.' );
			}
		}
		log::add('Optoma', 'error', 'Erreur de connexion au vidéoprojecteur');
		Return (FALSE);
}

    public function ParseOptoma($URL_control) {

    $html = file_get_html($URL_control);
    $rows = $html->find('tr');
    $bloc = array();

    if(count($rows)>1){
        for($i=1; $i<count($rows); $i++){
            if (isset($rows[$i]->find('td[class="proj"]',0)->plaintext) | !empty($rows[$i]->find('td[class="proj"]',0)->plaintext)) {
            $infobase = $rows[$i]->find('td[class="proj"]',0)->plaintext;
            }else { $infobase = ""; }
            if (isset($rows[$i]->find('td[class="proj"] input',0)->value) | !empty($rows[$i]->find('td[class="proj"] input',0)->value)) {
            $infosecond = $rows[$i]->find('td[class="proj"] input',0)->value; 
            }else { $infosecond = ""; }
            if (isset($rows[$i]->find('td[id] font',0)->plaintext) | !empty($rows[$i]->find('td[id] font',0)->plaintext)) {
            $information = $rows[$i]->find('td[id] font',0)->plaintext; 
            }else { $information = ""; }
            if (isset($rows[$i]->find('td[style] input[type=text]',0)->value) | !empty($rows[$i]->find('td[style] input[type=text]',0)->value)) {
            $champtext = $rows[$i]->find('td[style] input[type=text]',0)->value; 
            }else { $champtext = ""; }
            if (isset($rows[$i]->find('td[style] select [option*]',0)->plaintext) | !empty($rows[$i]->find('td[style] select [option*]',0)->plaintext)) {
            $listoption = $rows[$i]->find('td[style] select [option*]',0)->plaintext; 
            }else { $listoption = ""; }
            if (isset($rows[$i]->find('td[style] select [option SELECTED]',0)->plaintext) | !empty($rows[$i]->find('td[style] select [option SELECTED]',0)->plaintext)) {
            $selection = $rows[$i]->find('td[style] select [option SELECTED]',0)->plaintext;
            }else { $selection = ""; }

            $rawbloc = array("rank"=>$i, "infobase"=>trim($infobase), "infosecond"=>trim($infosecond), "information"=>trim($information), "champtext"=>trim($champtext), "listoption"=>trim($listoption), "selection"=>trim($selection));
            array_push($bloc, $rawbloc);
			//log::add('Optoma', 'debug', 'Bloc ' . $bloc['0'][infosecond]);

        }
    }
    return ( $bloc ); 
    //print $rawbloc['infosecond']['Resync'];
    $html->clear(); 
	}
    public function StateOptoma($URL_control) {
      
    $html = file_get_html($URL_control);
    $rows = $html->find('input[id=pwr]',0)->value;
    if($rows>1){
      $rows=0;
      }
      return $rows;
      $html->clear(); 
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
		static $VPcontrol = array();
    	static $VPstate;
    	static $RAW;

    	$URL_form_login = 'http://' . $this->getConfiguration('AdrIP') . '/login.asp';
		$URL_action_login = 'http://' . $this->getConfiguration('AdrIP') . '/Info.asp';
		$URL_control = 'http://' . $this->getConfiguration('AdrIP') . '/control.asp';
		$URL_CGI = $this->getConfiguration('ControlCGI');
      
		$VPcookies = $this->login();
		if ( $VPcookies == FALSE ) // teste si authentification OK
			return;
      
		switch ($cmd){
		case 'On':
			log::add('Optoma', 'debug', 'Commande On - '. $cmd );
   			$postValues = array( 'request_projector_info(this, 0)' => "btn_powon" );

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $URL_control);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
			$content = curl_exec($curl);
			curl_close($curl);
			
			log::add('Optoma', 'debug', 'control.asp (btn_powon=Power On) = ' /*. $content*/ );
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
			
			log::add('Optoma', 'debug', 'control.asp (pwr=off) = ' . $content );
			$this->checkAndUpdateCmd('Power', 0);
			break;
		
		case 'Refresh':
		
			log::add('Optoma', 'debug', 'Commande Refresh');
            $methodCGI = $this->getConfiguration('askCGI');
            $methodWebParsing = $this->getConfiguration('askWebParsing');
            $methodTelnet = $this->getConfiguration('askTelnet');
            $methodPJLink = $this->getConfiguration('askPJLink');
            log::add('Optoma', 'debug', 'CGI : ' . $methodCGI . ' WebParsing : ' . $methodWebParsing . ' Telnet : ' . $methodTelnet . ' PJ Link : ' . $methodPJLink );

            if ($methodCGI == 1) {
                $RAW = $this->getCGI($URL_CGI);
              	foreach ($RAW as $key => $value) {
					$this->checkAndUpdateCmd($key, $value);
					log::add('Optoma', 'debug', 'Key : ' . $key . ' Value : ' . $value);
				}
            	//$this->checkAndUpdateCmd('Power', $RAW['Powerstatus']);
            }

			if ($methodWebParsing == 1) {
                $postValues = array( 'login' => $this->getConfiguration('UserId'),'password' => $this->getConfiguration('MdP') );

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $URL_action_login);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_REFERER, $URL_form_login);
                $content = curl_exec($curl);
                curl_close($curl);
              
                $VPdata = $this->DecodeEtat($content);
                $VPcontrol = $this->ParseOptoma($URL_control);
                $VPstate = $this->StateOptoma($URL_control);

            	// Page control
            	$this->checkAndUpdateCmd('Powerstatus', $VPstate);
                $this->checkAndUpdateCmd('AV Mute', $VPcontrol['2'][information]);
                $this->checkAndUpdateCmd('Freeze', $VPcontrol['3'][information]);
                $this->checkAndUpdateCmd('Information hide', $VPcontrol['4'][information]);
                $this->checkAndUpdateCmd('High Altitude', $VPcontrol['5'][information]);
                $this->checkAndUpdateCmd('Keypad Lock', $VPcontrol['6'][information]);
                $this->checkAndUpdateCmd('Display Mode Lock', $VPcontrol['7'][information]);
                $this->checkAndUpdateCmd('Direct Power On', $VPcontrol['8'][information]);
                $this->checkAndUpdateCmd('3D Sync. Invert', $VPcontrol['9'][information]);
               // $this->checkAndUpdateCmd('3DModeOnOffStatus', $VPcontrol['10'][information]);
                $this->checkAndUpdateCmd('3D Mode', $VPcontrol['11'][selection]);
                $this->checkAndUpdateCmd('3D-2D', $VPcontrol['12'][selection]);
                $this->checkAndUpdateCmd('3D Format', $VPcontrol['13'][selection]);
                $this->checkAndUpdateCmd('Internal Speaker', $VPcontrol['14'][information]);
                $this->checkAndUpdateCmd('Mute', $VPcontrol['15'][information]);
                $this->checkAndUpdateCmd('Dynamic Black', $VPcontrol['16'][information]);
                $this->checkAndUpdateCmd('Volume(Audio)', $VPcontrol['17'][champtext]);
                $this->checkAndUpdateCmd('Volume(Mic)', $VPcontrol['18'][champtext]);
                $this->checkAndUpdateCmd('Audio Input', $VPcontrol['19'][selection]);
                $this->checkAndUpdateCmd('Source', $VPcontrol['20'][selection]);
                $this->checkAndUpdateCmd('Brightness', $VPcontrol['21'][champtext]);
                $this->checkAndUpdateCmd('Contrast', $VPcontrol['22'][champtext]);
                $this->checkAndUpdateCmd('Sharpness', $VPcontrol['23'][champtext]);
                $this->checkAndUpdateCmd('Brilliant Color', $VPcontrol['25'][champtext]);
                $this->checkAndUpdateCmd('Color Temperature', $VPcontrol['27'][selection]);
                $this->checkAndUpdateCmd('Display Mode', $VPcontrol['28'][selection]);
                $this->checkAndUpdateCmd('Color Space', $VPcontrol['29'][selection]);
                $this->checkAndUpdateCmd('12V Trigger', $VPcontrol['30'][selection]);
                $this->checkAndUpdateCmd('Aspect Ratio', $VPcontrol['31'][selection]);
                $this->checkAndUpdateCmd('Screen Type', $VPcontrol['32'][selection]);
                $this->checkAndUpdateCmd('Projection', $VPcontrol['33'][selection]);
                $this->checkAndUpdateCmd('Zoom Value', $VPcontrol['34'][champtext]);
                $this->checkAndUpdateCmd('H. Keystone', $VPcontrol['35'][champtext]);
                $this->checkAndUpdateCmd('V. Keystone', $VPcontrol['36'][champtext]);
                $this->checkAndUpdateCmd('H.Image Shift', $VPcontrol['37'][champtext]);
                $this->checkAndUpdateCmd('V.Image Shift', $VPcontrol['38'][champtext]);
               // $this->checkAndUpdateCmd('FourCornersStatus', $VPcontrol['39'][selection]);
                $this->checkAndUpdateCmd('Sleep Timer(min.)', $VPcontrol['40'][champtext]);
                $this->checkAndUpdateCmd('Projector ID', $VPcontrol['41'][champtext]);
                $this->checkAndUpdateCmd('Remote Code', $VPcontrol['42'][champtext]);
                $this->checkAndUpdateCmd('Background Color', $VPcontrol['43'][selection]);
                $this->checkAndUpdateCmd('Wall Color', $VPcontrol['44'][selection]);
                $this->checkAndUpdateCmd('Logo', $VPcontrol['45'][selection]);
                $this->checkAndUpdateCmd('Power mode', $VPcontrol['46'][selection]);
                $this->checkAndUpdateCmd('Brightness Mode', $VPcontrol['47'][selection]);
            }
            
            if ($methodTelnet == 1) {
              
            }
            
            if ($methodPJLink == 1) {
              
            }

            // Page index
            $this->checkAndUpdateCmd('Model', $VPdata[Model]);
            $this->checkAndUpdateCmd('Firmware', $VPdata[Firmware]);
            $this->checkAndUpdateCmd('LANVersion', $VPdata[LANVersion]);
            $this->checkAndUpdateCmd('IPAddress', $VPdata[IPAddress]);
            $this->checkAndUpdateCmd('SubnetMask', $VPdata[SubnetMask]);
            $this->checkAndUpdateCmd('MACAddress', $VPdata[MACAddress]);
		}

  //  $cmd->save();
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

		log::add('Optoma', 'debug', 'Parsing index.asp - ' . $data[Model] . ' Firmware=' . $data[Firmware] . ' LANVersion=' . $data[LANVersion] . ' IPAddress=' . $data[IPAddress] . ' SubnetMask=' . $data[SubnetMask] . ' MACAddress=' . $data[MACAddress]);
		
		return ($data);
	}
  
	public static function amxDeviceDiscovery() {
		log::add('Optoma', 'debug', 'Recherche du vidéoprojecteur en cours...');
		error_reporting(E_ALL);
        $port = 9131;
        $addressIP = '239.255.250.250';
        $socketClient = socket_create(AF_INET, SOCK_DGRAM, 0);
        if ($socketClient === false) {
             Return FALSE;
        }
        socket_set_option($socketClient, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($socketClient, $addressIP, $port);
        $tab_mcast = array("group" => $addressIP, "interface" => 0,);
        socket_set_option($socketClient, IPPROTO_IP, MCAST_JOIN_GROUP, $tab_mcast);
        if (false === ($bytes = socket_recvfrom($socketClient, $buf, 800, 0, $from, $fromPort))) {
            Return FALSE;
        }
        socket_close($socketClient);
        //echo $from;
		log::add('Optoma', 'debug', 'Vidéoprojecteur trouvé : '. $from);
      	return $from;
	}
  
  	public function searchCGILink() {
		foreach(eqLogic::byType('Optoma') as $Optoma){
			if($Optoma->getIsEnable() && $Optoma->getConfiguration('AdrIP') != ''){
				$AdrIP = $Optoma->getConfiguration('AdrIP');
			}
		}
       		$URL_control = "http://" . $AdrIP . "/control.asp";
        	log::add('Optoma', 'debug', 'Recherche du lien control CGI en cours...');

			$html = file_get_html($URL_control);
        	if (isset($html->find('script[language=javascript]',0)->src) | !empty($html->find('script[language=javascript]',0)->src)) {
        		$js_link = $html->find('script[language=javascript]',0)->src;
        	}
        	else {
				$js_link = 'js/control.js';
			}
	        $URL_js_file = 'http://' . $AdrIP . '/' . $js_link;
        	$js = file_get_html($URL_js_file);
        	$deb = strpos($js, "POST");
        	$fin = strpos($js, ');', $deb);
        	$cgi_link = substr($js, $deb, $fin-$deb);
        	preg_match_all("/(\"\/)(.*)\/(.*)(\")/", $cgi_link, $matches, PREG_PATTERN_ORDER);
			$URL_cgi = 'http://' . $AdrIP . $matches[0][0];

      	$URL_cgi = str_replace("\"", "", $URL_cgi);
		log::add('Optoma', 'debug', 'Lien trouvé : '. $URL_cgi);
		return $URL_cgi;
	}
  
   public static function getCGI($URL_CGI) {
        $data = file_get_html($URL_CGI);
        $cleandata = str_replace("<html> {", "",$data);
        $cleanerdata = str_replace("}", "",$cleandata);
		$arr = array();
		$pairs = explode(',', $cleanerdata);
        foreach ($pairs as $i) {
        	list($name,$value) = explode(':', $i, 2);
        	$arr[$name] = $value;
        }
		
		$source = array("Powerstatus" => "pw","Source" => "a","Display Mode" => "b",
                          "Brightness" => "c","Contrast" => "d","Sharpness" => "f","Projection" => "t",
                          "Brightness Mode" => "h","Mute" => "j","AV Mute" => "k","Power mode" => "l",
                          "Volume Audio" => "m","Freeze" => "n","Logo" => "o","3D-2D" => "p",
                          "Color Space" => "q","Zoom Value" => "r","3D Mode" => "w","Background Color" => "x",
                          "Wall Color" => "y","Volume Mic" => "z","Phase" => "A","Brilliant Color" => "B",
                          "Gamma" => "C","Color Temperature" => "D","3D Format" => "E",
                          "Internal Speaker" => "F","12V Trigger" => "G","Sleep Timer min." => "H",
                          "Audio Input" => "I","H. Keystone" => "J","V. Keystone" => "K","Aspect Ratio" => "L",
                          "H.Image Shift" => "M","V.Image Shift" => "N","High Altitude" => "O",
                          "Direct Power On" => "P","Projector ID" => "Q","Remote Code" => "R",
                          "Screen Type" => "S","3D Sync. Invert" => "T","Power" => "U",
                          "Information hide" => "V","Display Mode Lock" => "W","Dynamic Black" => "X",
                          "Keypad Lock" => "Y","button_up" => "e","button_down" => "g","button_left" => "i",
                          "button_right" => "s","locking Source" => "Z","ISF" => "isf");

        foreach( $arr as $key => $value ){
            if (is_array($source)) {
                foreach( $source as $cle => $valeur ){
                    if ($valeur === $key) {
                        $donnee[$cle] = $value;
                    }

                }
            }
        }
		return $donnee;
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
			log::add('Optoma', 'debug', 'Exécution de la commande Refresh');
			$eqLogic->call_vdp ('Refresh');

		}
	}
}
