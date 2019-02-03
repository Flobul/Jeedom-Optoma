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

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     
*/
     public static function cronHourly () {
     foreach (self::byType('Optoma') as $optoma) {//parcours tous les équipements du plugin vdm
       if ($optoma->getIsEnable() == 1) {//vérifie que l'équipement est actif
         $cmd = $optoma->getCmd(null, 'Refresh');//retourne la commande "refresh si elle exxiste
         if (!is_object($cmd)) {//Si la commande n'existe pas
           continue; //continue la boucle
         }
         $cmd->execCmd(); // la commande existe on la lance
       }
     }
     }

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

    public function postSave() {
    		$cmd = $this->getCmd(null, 'Refresh'); // On recherche la commande refresh de l’équipement
    		if (is_object($cmd)) { //elle existe et on lance la commande
    			 $cmd->execCmd();
    		}
    }

    public function postUpdate() {

    }        
    

    public function preUpdate() {
		if (empty($this->getConfiguration('AdrIP'))) {
			throw new Exception(__('L\'adresse IP ne peut pas être vide',__FILE__));
		}

		if (empty($this->getConfiguration('UserId'))) {
			throw new Exception(__('L\'utilisateur ne peut être vide',__FILE__));
		}
    }

    public function preSave() {
		if ( $this->getIsEnable() ){
			log::add('Optoma', 'debug', 'Création des commandes dans le postSave');

			// Information Power On/Off 
			$info = $this->getCmd(null, 'Power');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
                $info->setName('Power');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('Power');
                $info->setType('info');
                $info->setSubType('binary');
                $info->setOrder(1);
                $info->setIsVisible(1);
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
                $cmd->setOrder(2);
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
			if ( ! is_object($info)) {
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
			if ( ! is_object($info)) {
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
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Version LAN');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('LANVersion');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
            }
/*
			// Information IPAddress 
			$info = $this->getCmd(null, 'IPAddress');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
      }
				$info->setName('Adresse IP');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('IPAddress');
				$info->setType('info');
				$info->setSubType('string');
        $info->setIsVisible(0);
				$info->save();
			
			// Information SubnetMask 
			$info = $this->getCmd(null, 'SubnetMask');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
      }
				$info->setName('Masque de sous-réseau');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('SubnetMask');
				$info->setType('info');
				$info->setSubType('string');
        $info->setIsVisible(0);
				$info->save();

			// Information MACAddress 
			$info = $this->getCmd(null, 'MACAddress');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
      }
				$info->setName('Adresse MAC');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('MACAddress');
				$info->setType('info');
				$info->setSubType('string');
        $info->setIsVisible(0);
				$info->save();
*/
			// Information AV Mute 
			$info = $this->getCmd(null, 'AVMute');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('AV Mute');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('AVMuteStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
          
			// Information Freeze
			$info = $this->getCmd(null, 'Freeze');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Freeze');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('FreezeStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
/*			
          	// Information Information Hide
			$info = $this->getCmd(null, 'InfoHide');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Information Hide');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('InfoHideStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
			}
          
          	// Information High Altitude
			$info = $this->getCmd(null, 'HighAltitude');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('High Altitude');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('HighAltitudeStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
			          
          	// Information Keypad Lock
			$info = $this->getCmd(null, 'KeypadLock');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Keypad Lock');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('KeypadLockStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
			          
          	// Information Display Mode Lock
			$info = $this->getCmd(null, 'DisplayModeLock');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Display Mode Lock');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('DisplayModeLockStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
                }
*/			          
          	// Information Direct Power On
			$info = $this->getCmd(null, 'DirectPowerOn');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Direct Power On');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('DirectPowerOnStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
			          
          	// Information 3D Sync. Invert
			$info = $this->getCmd(null, '3DSyncInvert');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Sync Invert');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3DSyncInvertStatus');
				$info->setType('info');
				$info->setSubType('binary');
        		$info->setIsVisible(0);
				$info->save();
            }
			
      // Information 3D Mode
			$info = $this->getCmd(null, '3DModeOnOff');
			if ( ! is_object($info)) {
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
			$info = $this->getCmd(null, '3DMode');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Mode');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3DModeStatus');
				$info->setType('info');
				$info->setOrder(10);
				$info->setSubType('string');
				$info->save();
            }

        // Information 3D-2D
			$info = $this->getCmd(null, '3D2D');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D-2D');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3D2DStatus');
				$info->setType('info');
				$info->setSubType('string');
				$info->setIsVisible(0);
				$info->save();
            }

        // Information 3D Format
			$info = $this->getCmd(null, '3DFormat');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('3D Format');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('3DFormatStatus');
				$info->setType('info');
				$info->setSubType('string');
				$info->setOrder(11);
				$info->setIsVisible(1);
				$info->save();
            }
        
      // Information Internal Speaker
			$info = $this->getCmd(null, 'InternalSpeaker');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Internal Speaker');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('InternalSpeakerStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }        
						
            // Information Mute
			$info = $this->getCmd(null, 'Mute');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Mute');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('MuteStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setIsVisible(0);
				$info->save();
            }
						
      // Information Dynamic Black
			$info = $this->getCmd(null, 'DynamicBlack');
			if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Dynamic Black');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('DynamicBlackStatus');
				$info->setType('info');
				$info->setSubType('string');
        		$info->setOrder(8);
        		$info->setIsVisible(1);
				$info->save();
            }

        // Information Volume(Audio)
      		$info = $this->getCmd(null, 'VolumeAudio');
      		if ( ! is_object($info)) {
				$info = new OptomaCmd();
				$info->setName('Volume(Audio)');
				$info->setEqLogic_id($this->getId());
				$info->setLogicalId('VolumeAudioStatus');
				$info->setType('info');
				$info->setSubType('numeric');
				$info->setIsVisible(0);
				$info->save();
            }
/*
        // Information Volume(Mic)
      $info = $this->getCmd(null, 'VolumeMic');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Volume(Mic)');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('VolumeMicStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
}
        // Information Audio Input
      $info = $this->getCmd(null, 'AudioInput');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Audio Input');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('AudioInputStatus');
        $info->setType('info');
        $info->setSubType('string');
        $info->setIsVisible(0);
        $info->save();
        }
*/
        // Information Source
      		$info = $this->getCmd(null, 'Source');
      		if ( ! is_object($info)) {
      			$info = new OptomaCmd();
      			$info->setName('Source');
      			$info->setEqLogic_id($this->getId());
      			$info->setLogicalId('SourceStatus');
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
                $info->setLogicalId('BrightnessStatus');
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
                $info->setLogicalId('ContrastStatus');
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
                $info->setLogicalId('SharpnessStatus');
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
                $info->setLogicalId('PhaseStatus');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }

        // Information Brilliant Color
      		$info = $this->getCmd(null, 'BrilliantColor');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Brilliant Color');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('BrilliantColorStatus');
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
                $info->setLogicalId('GammaStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }

        // Information Color Temperature
      		$info = $this->getCmd(null, 'ColorTemperature');
      		if ( ! is_object($info)) {
      			$info = new OptomaCmd();
                $info->setName('Color Temperature');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('ColorTemperatureStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }

        // Information Display Mode
      		$info = $this->getCmd(null, 'DisplayMode');
      		if ( ! is_object($info)) {
                 $info = new OptomaCmd();
                $info->setName('Display Mode');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('DisplayModeStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(5);
                $info->setIsVisible(1);
                $info->save();
            }

        // Information Color Space
      		$info = $this->getCmd(null, 'ColorSpace');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Color Space');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('ColorSpaceStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
/*
        // Information 12V Trigger
      $info = $this->getCmd(null, '12VTrigger');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('12V Trigger');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('12VTriggerStatus');
        $info->setType('info');
        $info->setSubType('string');
        $info->setIsVisible(0);
        $info->save();
        }
*/
        // Information Aspect Ratio
      		$info = $this->getCmd(null, 'AspectRatio');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Aspect Ratio');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('AspectRatioStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(6);
                $info->setIsVisible(1);
                $info->save();
            }

        // Information Screen Type
      		$info = $this->getCmd(null, 'ScreenType');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Screen Type');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('ScreenTypeStatus');
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
                $info->setLogicalId('ProjectionStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }
/*
        // Information Zoom Value
      $info = $this->getCmd(null, 'ZoomValue');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Zoom Value');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('ZoomValueStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information H. Keystone
      $info = $this->getCmd(null, 'HKeystone');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('H. Keystone');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('HKeystoneStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information V. Keystone
      $info = $this->getCmd(null, 'VKeystone');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('V. Keystone');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('VKeystoneStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information H.Image Shift
      $info = $this->getCmd(null, 'HImageShift');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('H.Image Shift');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('HImageShiftStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information V.Image Shift
      $info = $this->getCmd(null, 'VImageShift');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('V.Image Shift');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('VImageShiftStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information Four Corners
      $info = $this->getCmd(null, 'FourCorners');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Four Corners');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('FourCornersStatus');
        $info->setType('info');
        $info->setSubType('string');
        $info->setIsVisible(0);
        $info->save();
        }
*/
        // Information Sleep Timer
      		$info = $this->getCmd(null, 'SleepTimer');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Sleep Timer');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('SleepTimerStatus');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }

        // Information Projector ID
      		$info = $this->getCmd(null, 'ProjectorID');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Projector ID');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('ProjectorIDStatus');
                $info->setType('info');
                $info->setSubType('numeric');
                $info->setIsVisible(0);
                $info->save();
            }
/*
        // Information Remote Code
      $info = $this->getCmd(null, 'RemoteCode');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Remote Code');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('RemoteCodeStatus');
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information Background Color
      $info = $this->getCmd(null, 'BackgroundColor');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Background Color');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('BackgroundColorStatus');
        $info->setType('info');
        $info->setSubType('string');
        $info->setIsVisible(0);
        $info->save();
        }

        // Information Wall Color
      $info = $this->getCmd(null, 'WallColor');
      if ( ! is_object($info)) {
        $info = new OptomaCmd();
        $info->setName('Wall Color');
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('WallColorStatus');
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
        $info->setLogicalId('LogoStatus');
        $info->setType('info');
        $info->setSubType('string');
        $info->setIsVisible(0);
        $info->save();
        }
*/
        // Information Power mode
      		$info = $this->getCmd(null, 'PowerMode');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Power mode');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('PowerModeStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setIsVisible(0);
                $info->save();
            }

        // Information Brightness mode
      		$info = $this->getCmd(null, 'BrightnessMode');
      		if ( ! is_object($info)) {
                $info = new OptomaCmd();
                $info->setName('Brightness Mode');
                $info->setEqLogic_id($this->getId());
                $info->setLogicalId('BrightnessModeStatus');
                $info->setType('info');
                $info->setSubType('string');
                $info->setOrder(7);
                $info->setIsVisible(1);
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
            Return (FALSE);
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


    //print_r ( $rawbloc );
        }
      			//log::add('Optoma', 'debug', 'Bloc ' . $bloc['0'][infosecond]);


    }
    //print_r ( $bloc ); // affiche par tableau $bloc OKOKOKOKOKOK

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

    $URL_form_login = 'http://' . $this->getConfiguration('AdrIP') . '/login.asp';
		$URL_action_login = 'http://' . $this->getConfiguration('AdrIP') . '/Info.asp';
		$URL_control = 'http://' . $this->getConfiguration('AdrIP') . '/control.asp';
      
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

//			log::add('Optoma', 'debug', 'Decodage Info.asp = ' . $content );
			$VPdata = $this->DecodeEtat($content);
      $VPcontrol = $this->ParseOptoma($URL_control);
      $VPstate = $this->StateOptoma($URL_control);
	
		default:
    $this->checkAndUpdateCmd('AVMute', $VPcontrol['2'][infosecond]);
      $this->checkAndUpdateCmd('AVMuteStatus', $VPcontrol['2'][information]);
    $this->checkAndUpdateCmd('Freeze', $VPcontrol['3'][infosecond]);
      $this->checkAndUpdateCmd('FreezeStatus', $VPcontrol['3'][information]);
//    $this->checkAndUpdateCmd('InfoHide', $VPcontrol['4'][infosecond]);
//      $this->checkAndUpdateCmd('InfoHideStatus', $VPcontrol['4'][information]);
//    $this->checkAndUpdateCmd('HighAltitude', $VPcontrol['5'][infosecond]);
//      $this->checkAndUpdateCmd('HighAltitudeStatus', $VPcontrol['5'][information]);
//    $this->checkAndUpdateCmd('KeypadLock', $VPcontrol['6'][infosecond]);
//      $this->checkAndUpdateCmd('KeypadLockStatus', $VPcontrol['6'][information]);
//    $this->checkAndUpdateCmd('DisplayModeLock', $VPcontrol['7'][infosecond]);
//      $this->checkAndUpdateCmd('DisplayModeLockStatus', $VPcontrol['7'][information]);
    $this->checkAndUpdateCmd('DirectPowerOn', $VPcontrol['8'][infosecond]);
      $this->checkAndUpdateCmd('DirectPowerOnStatus', $VPcontrol['8'][information]);
    $this->checkAndUpdateCmd('3DSyncInvert', $VPcontrol['9'][infosecond]);
      $this->checkAndUpdateCmd('3DSyncInvertStatus', $VPcontrol['9'][information]);
    $this->checkAndUpdateCmd('3DModeOnOff', $VPcontrol['10'][infosecond]);
      $this->checkAndUpdateCmd('3DModeOnOffStatus', $VPcontrol['10'][information]);
    $this->checkAndUpdateCmd('3DMode', $VPcontrol['11'][infobase]);
      $this->checkAndUpdateCmd('3DModeStatus', $VPcontrol['11'][selection]);
		$this->checkAndUpdateCmd('3D2D', $VPcontrol['12'][infobase]);
      $this->checkAndUpdateCmd('3D2DStatus', $VPcontrol['12'][selection]);
		$this->checkAndUpdateCmd('3DFormat', $VPcontrol['13'][infobase]);
      $this->checkAndUpdateCmd('3DFormatStatus', $VPcontrol['13'][selection]);
    $this->checkAndUpdateCmd('InternalSpeaker', $VPcontrol['14'][infosecond]);
      $this->checkAndUpdateCmd('InternalSpeakerStatus', $VPcontrol['14'][information]);
    $this->checkAndUpdateCmd('Mute', $VPcontrol['15'][infosecond]);
      $this->checkAndUpdateCmd('MuteStatus', $VPcontrol['15'][information]);
    $this->checkAndUpdateCmd('DynamicBlack', $VPcontrol['16'][infosecond]);
      $this->checkAndUpdateCmd('DynamicBlackStatus', $VPcontrol['16'][information]);
		$this->checkAndUpdateCmd('VolumeAudio', $VPcontrol['17'][infobase]);
      $this->checkAndUpdateCmd('VolumeAudioStatus', $VPcontrol['17'][champtext]);
//		$this->checkAndUpdateCmd('VolumeMic', $VPcontrol['18'][infobase]);
//      $this->checkAndUpdateCmd('VolumeMicStatus', $VPcontrol['18'][champtext]);
//		$this->checkAndUpdateCmd('AudioInput', $VPcontrol['19'][infobase]);
//      $this->checkAndUpdateCmd('AudioInputStatus', $VPcontrol['19'][selection]);
		$this->checkAndUpdateCmd('Source', $VPcontrol['20'][infobase]);
			$this->checkAndUpdateCmd('SourceStatus', $VPcontrol['20'][selection]);
    $this->checkAndUpdateCmd('Brightness', $VPcontrol['21'][infobase]);
			$this->checkAndUpdateCmd('BrightnessStatus', $VPcontrol['21'][champtext]);
    $this->checkAndUpdateCmd('Contrast', $VPcontrol['22'][infobase]);
      $this->checkAndUpdateCmd('ContrastStatus', $VPcontrol['22'][champtext]);
    $this->checkAndUpdateCmd('Sharpness', $VPcontrol['23'][infobase]);
			$this->checkAndUpdateCmd('SharpnessStatus', $VPcontrol['23'][champtext]);
    $this->checkAndUpdateCmd('Phase', $VPcontrol['24'][infobase]);
			$this->checkAndUpdateCmd('PhaseStatus', $VPcontrol['24'][champtext]);
    $this->checkAndUpdateCmd('BrilliantColor', $VPcontrol['25'][infobase]);
			$this->checkAndUpdateCmd('BrilliantColorStatus', $VPcontrol['25'][champtext]);
    $this->checkAndUpdateCmd('Gamma', $VPcontrol['26'][infobase]);
			$this->checkAndUpdateCmd('GammaStatus', $VPcontrol['26'][selection]);
    $this->checkAndUpdateCmd('ColorTemperature', $VPcontrol['27'][infobase]);
			$this->checkAndUpdateCmd('ColorTemperatureStatus', $VPcontrol['27'][selection]);
    $this->checkAndUpdateCmd('DisplayMode', $VPcontrol['28'][infobase]);
		  $this->checkAndUpdateCmd('DisplayModeStatus', $VPcontrol['28'][selection]);
//    $this->checkAndUpdateCmd('ColorSpace', $VPcontrol['29'][infobase]);
//			$this->checkAndUpdateCmd('ColorSpaceStatus', $VPcontrol['29'][selection]);
//    $this->checkAndUpdateCmd('12VTrigger', $VPcontrol['30'][infobase]);
//			$this->checkAndUpdateCmd('12VTriggerStatus', $VPcontrol['30'][selection]);
    $this->checkAndUpdateCmd('AspectRatio', $VPcontrol['31'][infobase]);
			$this->checkAndUpdateCmd('AspectRatioStatus', $VPcontrol['31'][selection]);
    $this->checkAndUpdateCmd('ScreenType', $VPcontrol['32'][infobase]);
			$this->checkAndUpdateCmd('ScreenTypeStatus', $VPcontrol['32'][selection]);
    $this->checkAndUpdateCmd('Projection', $VPcontrol['33'][infobase]);
			$this->checkAndUpdateCmd('ProjectionStatus', $VPcontrol['33'][selection]);
//    $this->checkAndUpdateCmd('ZoomValue', $VPcontrol['34'][infobase]);
//			$this->checkAndUpdateCmd('ZoomValueStatus', $VPcontrol['34'][champtext]);
//    $this->checkAndUpdateCmd('HKeystone', $VPcontrol['35'][infobase]);
//			$this->checkAndUpdateCmd('HKeystoneStatus', $VPcontrol['35'][champtext]);
//    $this->checkAndUpdateCmd('VKeystone', $VPcontrol['36'][infobase]);
//			$this->checkAndUpdateCmd('VKeystoneStatus', $VPcontrol['36'][champtext]);
//    $this->checkAndUpdateCmd('HImageShift', $VPcontrol['37'][infobase]);
//			$this->checkAndUpdateCmd('HImageShiftStatus', $VPcontrol['37'][champtext]);
//    $this->checkAndUpdateCmd('VImageShift', $VPcontrol['38'][infobase]);
//			$this->checkAndUpdateCmd('VImageShiftStatus', $VPcontrol['38'][champtext]);
//    $this->checkAndUpdateCmd('FourCorners', $VPcontrol['39'][infobase]);
//			$this->checkAndUpdateCmd('FourCornersStatus', $VPcontrol['39'][selection]);
    $this->checkAndUpdateCmd('SleepTimer', $VPcontrol['40'][infobase]);
			$this->checkAndUpdateCmd('SleepTimerStatus', $VPcontrol['40'][champtext]);
    $this->checkAndUpdateCmd('ProjectorID', $VPcontrol['41'][infobase]);
			$this->checkAndUpdateCmd('ProjectorIDStatus', $VPcontrol['41'][champtext]);
//    $this->checkAndUpdateCmd('RemoteCode', $VPcontrol['42'][infobase]);
//			$this->checkAndUpdateCmd('RemoteCodeStatus', $VPcontrol['42'][champtext]);
//    $this->checkAndUpdateCmd('BackgroundColor', $VPcontrol['43'][infobase]);
//			$this->checkAndUpdateCmd('BackgroundColorStatus', $VPcontrol['43'][selection]);
//    $this->checkAndUpdateCmd('WallColor', $VPcontrol['44'][infobase]);
//			$this->checkAndUpdateCmd('WallColorStatus', $VPcontrol['44'][selection]);
//    $this->checkAndUpdateCmd('Logo', $VPcontrol['45'][infobase]);
//			$this->checkAndUpdateCmd('LogoStatus', $VPcontrol['45'][selection]);
    $this->checkAndUpdateCmd('PowerMode', $VPcontrol['46'][infobase]);
			$this->checkAndUpdateCmd('PowerModeStatus', $VPcontrol['46'][selection]);
    $this->checkAndUpdateCmd('BrightnessMode', $VPcontrol['47'][infobase]);
      $this->checkAndUpdateCmd('BrightnessModeStatus', $VPcontrol['47'][selection]);
      $this->checkAndUpdateCmd('Model', $VPdata[Model]);
			$this->checkAndUpdateCmd('Firmware', $VPdata[Firmware]);
			$this->checkAndUpdateCmd('LANVersion', $VPdata[LANVersion]);
//			$this->checkAndUpdateCmd('IPAddress', $VPdata[IPAddress]);
//			$this->checkAndUpdateCmd('SubnetMask', $VPdata[SubnetMask]);
//		  $this->checkAndUpdateCmd('MACAddress', $VPdata[MACAddress]);
      $this->checkAndUpdateCmd('Power', $VPstate);
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
