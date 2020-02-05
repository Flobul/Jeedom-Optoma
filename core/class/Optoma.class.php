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


    /*     * ***********************Methode static*************************** */

	public static function cron() {
		foreach(eqLogic::byType('Optoma') as $Optoma){
			if($Optoma->getIsEnable()){
				if ($Optoma->getConfiguration('RepeatCmd') == "cron") {
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

	public static function cron10() {
	foreach(eqLogic::byType('Optoma') as $Optoma){
		if($Optoma->getIsEnable()){
			if ($Optoma->getConfiguration('RepeatCmd') == "cron10"){
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

    public function preInsert() {
		$this->setCategory('multimedia', 1);
      	$this->setIsEnable(1);
		$this->setIsVisible(1);
    }

    public function postInsert() {

    }

    public function postSave() {
    /*	$cmd = $this->getCmd(null, 'Refresh');
    	if (is_object($cmd)) {
    		 $cmd->execCmd();
    	}*/
    }

    public function preSave() {

    }

    public function preUpdate() {
		if (empty($this->getConfiguration('AdrIP'))) {
			throw new Exception(__('L\'adresse IP ne peut pas être vide',__FILE__));
		}

		if ($this->getConfiguration('askCGI') == 0 && $this->getConfiguration('askTelnet') == 0 && $this->getConfiguration('askPJLink') == 0) {
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
				$info->setOrder(8);
				$info->setIsVisible(1);
				$info->save();
            }
			// Commande Mise sous tension (Power On)
			$cmd = $this->getCmd(null, 'Power On');
			if (!is_object($cmd)) {
				$cmd = new OptomaCmd();
                $cmd->setName('Power On');
                $cmd->setEqLogic_id($this->getId());
                $cmd->setLogicalId('Power On');
                $cmd->setType('action');
                $cmd->setSubType('other');
                $cmd->setIsVisible(1);
                $cmd->setOrder(2);
                $cmd->setTemplate('dashboard', 'PowerOnOff');
                $cmd->setDisplay('parameters',array ( "color" => "green", "type" => "off", "size" =>30 ));
                $cmd->setDisplay('showNameOndashboard','0');
                $cmd->setDisplay('showNameOnplan','0');
                $cmd->setDisplay('showNameOnview','0');
                $cmd->save();
            }
			// Commande Mise hors tension (Power Off)
			$cmd = $this->getCmd(null, 'Power Off');
			if (!is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('Power Off');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('Power Off');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setIsVisible(1);
				$cmd->setOrder(3);
				$cmd->setTemplate('dashboard', 'PowerOnOff');
				$cmd->setDisplay('parameters',array ( "color" => "green", "type" => "off", "size" =>30 ));
				$cmd->setDisplay('showNameOndashboard','0');
				$cmd->setDisplay('showNameOnplan','0');
				$cmd->setDisplay('showNameOnview','0');
				$cmd->save();
            }
			// Commande 3D Mode
			$cmd = $this->getCmd(null, '3D Mode');
			if (!is_object($cmd)) {
				$cmd = new OptomaCmd();
				$cmd->setName('3D Mode');
				$cmd->setEqLogic_id($this->getId());
				$cmd->setLogicalId('3D Mode');
				$cmd->setType('action');
				$cmd->setSubType('other');
				$cmd->setIsVisible(1);
				$cmd->setOrder(4);
				$cmd->save();
            }
			// Information Freeze
			$info = $this->getCmd(null, 'Freeze');
			if (!is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Freeze');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('Freeze');
				$info->setType('info');
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
				$info->setSubType('binary');
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
                $info->setSubType('binary');
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
                $info->setSubType('string');
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

    public function call_vdp( $cmd ) {
		static $VPcontrol = array();
    	static $VPstate;
    	static $RAW;

		$URL_action_login = 'http://' . $this->getConfiguration('AdrIP') . '/Info.asp';
		$URL_control = 'http://' . $this->getConfiguration('AdrIP') . '/control.asp';
		$URL_CGI = $this->getConfiguration('ControlCGI');

		switch ($cmd){

		case 'Refresh':

			log::add('Optoma', 'debug', 'Lancement commande Refresh');
            $methodCGI = $this->getConfiguration('askCGI');
            $methodTelnet = $this->getConfiguration('askTelnet');
            $methodPJLink = $this->getConfiguration('askPJLink');

            if ($methodCGI == 1) {
                $RAW = $this->getCGI($URL_CGI);
              	foreach ($RAW as $key => $value) {
					$this->checkAndUpdateCmd($key, $value);
				}
            }

            if ($methodTelnet == 1) {

            }

            if ($methodPJLink == 1) {

            }
          default:
            $cmd_action = self::devicesParameters('cmd_button.json');
			foreach( $cmd_action as $idname => $command ){
				if ($cmd == $command) {
					$command_nospace = str_replace(" ", "%20",$command);
					log::add('Optoma', 'debug', $URL_CGI."?".$idname."=".$command_nospace);
					$curl = curl_init();
					curl_setopt_array($curl, array(
                      CURLOPT_URL => $URL_CGI."?".$idname."=".$command_nospace,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_TIMEOUT => 1,
                      CURLOPT_CUSTOMREQUEST => "POST",
					));

					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);
					if ($err) {
						log::add('Optoma', 'debug', 'Erreur envoi commande: '.$err);
					}
				}
			}
            $regerg = self::devicesParameters('cmd_action.json');
            foreach ($regerg as $trt => $erfef) {
            log::add('Optoma', 'debug', $trt.' : commande1-commande2 : '.$erfef);
            }
		}
	}

	public static function amxDeviceDiscovery($_state) {
		log::add('Optoma', 'error', "Lancement du mode inclusion.");
		if ($_state == 1) {
		event::add('Optoma::includeDevice', null);
		if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))){
			log::add('Optoma', 'error', "Couldn't create socket: " . socket_strerror(socket_last_error($sock)));
			return false;
		}
		if( !socket_bind($sock, "0.0.0.0" , 9131) ){
			log::add('Optoma', 'error', "Couldn't bind port: " . socket_strerror(socket_last_error($sock)));
			return false;
		}
		if (!socket_set_option($sock, IPPROTO_IP, MCAST_JOIN_GROUP, array("group"=>"239.255.250.250","interface"=>0))) {
			log::add('Optoma', 'error', "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($sock)));
			return false;
		}
		socket_set_option($socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>60, "usec"=>0));
      		$start=time();
		while(true){
			$r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
			log::add('Optoma', 'debug', $remote_ip." : ".$remote_port." -- " . $buf);
          		self::DecodeAMXMessage($remote_ip,$buf);
          		if(time()-$start > 60)
					break;
		}
		socket_close($sock);
		event::add('Optoma::includeDevice', null);
        }
		else {
			log::add('Optoma', 'debug', 'Fin manuelle de l\'inclusion');
        }
	}

  	public static function DecodeAMXMessage($remote_ip,$buf){
        foreach(explode('<-',str_replace('>','',$buf)) as $param){
                $Make=explode('=',$param);
                if($Make[0]=="Make") {
                        $Type=str_replace(' ','_',$Make[1]);
						if ($Type != "Optoma") {
							log::add('Optoma', 'debug', 'Ce n\'est pas un Optoma: '.$Type);
                        }
                }
                if($Make[0]=="Model") {
                        $Model=str_replace(' ','_',$Make[1]);
                }
                if($Make[0]=="UUID") {
                        $UUID=str_replace(' ','_',$Make[1]);
                }
                if($Make[0]=="SDKClass") {
                        $SDKClass=str_replace(' ','_',$Make[1]);
						if ($SDKClass != "VideoProjector") {
							log::add('Optoma', 'debug', 'Ce n\'est pas un videoprojecteur: '.$SDKClass);
                        }
                }
		}
		if ($Type == 'Optoma') {
			Optoma::AddEquipement($Type." ".$Model,$UUID,$remote_ip);
        }
   	}

  public static function AddEquipement($Name,$_logicalId,$AdrIP){
		foreach(self::byLogicalId($_logicalId, 'Optoma',true) as $Equipement){
          		if (is_object($Equipement) && $Equipement->getConfiguration('AdrIP') == $AdrIP) {
          		return $Equipement;
			}
		}
    	$ControlCGI = Optoma::searchCGILink($AdrIP);
		if (isset($ControlCGI)) {
			$checkCGI = 1;
		}
		$Equipement = new Optoma();
		$Equipement->setName($Name."-".$_logicalId);
		$Equipement->setLogicalId($_logicalId);
		$Equipement->setObject_id(null);
		$Equipement->setEqType_name('Optoma');
		$Equipement->setIsEnable(1);
		$Equipement->setIsVisible(1);
		$Equipement->setConfiguration('model',$Name);
		$Equipement->setConfiguration('ControlCGI',$ControlCGI);
		$Equipement->setConfiguration('askCGI',$checkCGI);
		$Equipement->setConfiguration('mac',$_logicalId);
		$Equipement->setConfiguration('AdrIP',$AdrIP);
		$Equipement->save();
		config::save('include_mode', 0, 'Optoma');
		event::add('Optoma::includeDevice', $Equipement->getId());
		return $Equipement;
	}

	public static function searchCGILink($URL) {
		if (isset($URL)) {
			$AdrIP = $URL;
		}
		else {
			foreach(eqLogic::byType('Optoma') as $Optoma){
				if($Optoma->getIsEnable() && $Optoma->getConfiguration('AdrIP') != ''){
					$AdrIP = $Optoma->getConfiguration('AdrIP');
				}
			}
		}
		$URL_control = "http://" . $AdrIP . "/control.asp";
		log::add('Optoma', 'debug', 'Recherche du lien control CGI en cours...');
			$html = file_get_contents($URL_control);
			$array = array();
			preg_match( '/"javascript" src="(.*?)"/i', $html, $array ) ;
			$js_link = $array[1];
			if (!isset($js_link)) {
				return;
			}
			$URL_js_file = "http://" . $AdrIP . '/' . $js_link;
			$js = file_get_contents($URL_js_file);
			if (!@$js) {
				return;
			}
			$arr = array();
			preg_match( '/xmlhttp.open((.*?));/i', $js, $arr ) ;
			$prse = str_replace('"', '', explode(',', $arr[1])[1]);
			$URL_cgi = "http://" . $AdrIP . $prse;
			log::add('Optoma', 'debug', 'Lien trouvé : '. $URL_cgi);
			return $URL_cgi;
	}

	public static function getCGI($URL_CGI) {
		static $source = array("Powerstatus" => "pw","Source" => "a","Display Mode" => "b",
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
     	$option['Source'] = array("id" => "source","0" => "HDMI1","1" => "HDMI2","2" => "Component","3" => "VGA","4" => "Media");
		$option['Display Mode'] = array("id" => "dismode0","0" => "Cinema","1" => "HDR","2" => "HDR SIM","3" => "Game","4" => "Reference","5" => "Bright","6" => "User","7" => "ISF Day","8" => "ISF Night","9" => "3D","10" => "User(3D User)");
		$option['Projection'] = array("id" => "projection","0" => "Front-Desktop","1" => "Front-Ceiling  ","2" => "Rear-Desktop","3" => "Rear-Ceiling");
		$option['Brightness Mode'] = array("id" => "lampmd","0" => "Bright","1" => "Eco","2" => "Dynamic");
		$option['Power mode'] = array("id" => "pwmode","0" => "Eco","1" => "Active");
		$option['Logo'] = array("id" => "logo","0" => "Default","2" => "Neutral","1" => "User");
		$option['3D-2D'] = array("id" => "3dto2d","0" => "3D","1" => "L","2" => "R");
		$option['Color Space'] = array("id" => "colorsp0","0" => "Auto","1" => "RGB(0~255)","2" => "RGB(16~235)","3" => "YUV(0~255)","4" => "YUV(16~235)");
		$option['3D Mode'] = array("id" => "3dmode","1" => "DLP-Link","2" => "VESA 3D","3" => "NVIDIA 3D Vision","4" => "IR","0" => "Off");
		$option['Background Color'] = array("id" => "background","0" => "Reserved","1" => "Blue","2" => "Blaack","3" => "Red","4" => "Green","5" => "White");
		$option['Wall Color'] = array("id" => "wall","0" => "Off","1" => "Black board","2" => "Light Yellow","3" => "Light Green","4" => "Light Blue","5" => "Pink","6" => "Gray");
		$option['Gamma'] = array("id" => "Degamma","0" => "Film","1" => "Video","2" => "Graphics","3" => "Standard(2.2)","4" => "1.8","5" => "2.0","6" => "2.4","7" => "3D");
		$option['Color Temperature'] = array("id" => "colortmp","0" => "D55","1" => "D65","2" => "D75","3" => "D83","4" => "D93","5" => "Native");
		$option['3D Format'] = array("id" => "3dformat","0" => "Auto","1" => "SBS","2" => "Top and Bottom","4" => "Frame Packing");
		$option['12V Trigger'] = array("id" => "trigger","1" => "On","0" => "Off");
		$option['Audio Input'] = array("id" => "audio","0" => "Default","1" => "Audio Input 1","2" => "Audio Input 2","3" => "Audio Input 3");
		$option['Aspect Ratio'] = array("id" => "aspect0","L","0" => "4:3","1" => "16:9","2" => "Native","3" => "Auto");
		$option['Screen Type'] = array("id" => "screen","0" => "16:10","1" => "16:9");
		$option['Power'] = array("id" => "Power","0" => "100","1" => "95","2" => "90","3" => "85","4" => "80","255" => "Not Support");

        $data = file_get_contents($URL_CGI);
		log::add('Optoma', 'debug', 'Parse control_cgi : ' . $data);
		preg_match( '#{(.*)}#U', $data, $firstpass);
        $secondpass = str_replace("{", "",$firstpass[0]);
        $thirdpass = str_replace("}", "",$secondpass);
		$arr = array();
		$pairs = explode(',', $thirdpass);
        foreach ($pairs as $i) {
        	list($name,$value) = explode(':', $i, 2);
        	$arr[$name] = $value;
        }
		foreach( $arr as $key => $value ){
            if (is_array($source)) {
                foreach( $source as $cle => $valeur ){
                    if ($valeur === $key) {
                        $donnee[$cle] = $value;
                    }
                }
            }
        }
		foreach( $source as $cle => $valeur ){
			if (is_array($option[$cle])) {
				foreach( $option[$cle] as $key => $value ){
					if (str_replace('"', '',$donnee[$cle]) == $key)
						$donnee[$cle] = $value;
					}
				}
		}
		return $donnee;
		}

	public static function devicesParameters($filename) {
		$ModelVP = "UHD51";
		$return = array();
		$path = dirname(__FILE__) . '/../config/'. $ModelVP . '/';
      		log::add('Optoma', 'debug', 'Action sur ' . $path . $filename);

		$files = ls($path, '*.json', false, array('files', 'quiet'));
			foreach ($files as $file) {
              if ($file == $filename) {
				try {
					$content = file_get_contents($path . '/' . $file);
					if (is_json($content)) {
						$return += json_decode($content, true);
					}
				} catch (Exception $e) {

				}
              }
			}
		return $return;
    }
}

class OptomaCmd extends cmd {

    public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		switch ($this->getLogicalId()) {
		case 'Power On':
			$eqLogic->call_vdp ('Power On');
			break;
		case 'Power Off':
			$eqLogic->call_vdp ('Power Off');
			break;
		case '3D Mode':
			$eqLogic->call_vdp ('3D Mode');
			break;
		case 'Aspect Ratio':
			$eqLogic->call_vdp ('Aspect Ratio');
			break;
		case 'Brightness Mode':
			$eqLogic->call_vdp ('Brightness Mode');
			break;
		case 'Display Mode':
			$eqLogic->call_vdp ('Display Mode');
			break;
		case 'Source':
			$eqLogic->call_vdp ('Source');
			break;
		default:
			$eqLogic->call_vdp ('Refresh');
		}
		log::add('Optoma', 'debug', 'Action sur ' . $this->getLogicalId());
	}
}
