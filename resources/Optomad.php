#!/usr/
<?php

#$debug=1;
// require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../core/class/Optoma.class.php';
require_once dirname(__FILE__) . '/../core/class/Optomapi.class.php';

log::add('Optoma_Daemon', 'info', 'Activation du service Optoma');

$listen = config::byKey('listenport','Optoma');
$ipadr = config::byKey('ip','Optoma');

$telnet = new Optoma_telnet();
$errno = '';
$errstr = '';

 log::add('Optoma_Daemon', 'debug', 'Activation du daemon Optoma sur l\'IP '.$ipadr.'  port : '.$listen);
//echo 'Activation du daemon Optoma PARAMS :  ' . $errstr . '(IP : '.$ipadr.'  port : '.$listen.') '.date('H:i:s')."\n";

$eqLogics = eqLogic::byType('Optoma');
$refreshtime = 60;
$delain = time();
    $connect = $telnet->telnetConnect($ipadr, $listen, $errno, $errstr);
	if($connect) {
		while(true) {
			$telnet->telnetGetReadResponse($result);
			$state='';
			$info='';
			$reponse=explode("> ",$result);
			if(count($reponse) == 1)
				$value=$reponse[0];
			elseif(count($reponse) == 2)
				$value=$reponse[1];
			elseif(count($reponse) == 3)
				$value=$reponse[2];
			else log::add('Optoma', 'info','L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Réponse non appropriée ' . json_encode($reponse));
			if(preg_match('/[INFO]+([0-9]+)/i', $result, $matches)){
                $state = Optomapi::getError($matches[1]);
				/*switch($matches[1]) {
					case '0':
						$state="Standby Mode";
						break;
					case '1':
						$state="Warming up";
						break;
					case '2':
						$state="Cooling Down";
						break;
					case '3':
						$state="Out of Range";
						break;
					case '4':
						$state="Lamp Fail ( LED Fail)";
						break;
					case '5':
						$state="Thermal Switch Error";
						break;
					case '6':
						$state="Fan Lock";
						break;
					case '7':
						$state="Over Temperature";
						break;
					case '8':
						$state="Lamp Hours Running Out";
						break;
					case '9':
						$state="Cover Open";
						break;
					case '10':
						$state="Lamp Ignite Fail";
						break;
					case '11':
						$state="Format Board Power On Fail";
						break;
					case '12':
						$state="Color Wheel Unexpected Stop";
						break;
					case '13':
						$state="Over Temperature";
						break;
					case '14':
						$state="FAN 1 Lock";
						break;
					case '15':
						$state="FAN 2 Lock";
						break;
					case '16':
						$state="FAN 3 Lock";
						break;
					case '17':
						$state="FAN 4 Lock";
						break;
					case '18':
						$state="FAN 5 Lock";
						break;
					case '19':
						$state="LAN fail then restart";
						break;
					case '20':
						$state="LD lower than 60%";
						break;
					case '21':
						$state="LD NTC (1) Over Temperature";
						break;
					case '22':
						$state="LD NTC (2) Over Temperature";
						break;
					case '23':
						$state="High Ambient Temperature";
						break;
					case '24':
						$state="System Ready";
						break;
					default:
						$state="N/A";
				}*/
			}
			if(!empty($state)) {
				foreach ($eqLogics as $eqLogic) {
					if($eqLogic->getConfiguration('ip') == config::byKey('ip','Optoma')) {
						log::add('Optoma_Daemon', 'info', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Vidéoprojecteur trouvé, mise à jour de la valeur de SystemInfo');
						$systemInfo = $eqLogic->getCmd(null, 'SystemInfo');
						if(is_object($systemInfo)) {
							$systemInfo->event($state);
							log::add('Optoma_Daemon', 'info', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Valeur de SystemInfo mise à jour ' . $state);
						}
					}
				}
			}
		}
		usleep(500);
		if((time() - $delai) > $refreshtime)
			{
				// forcer actualisation
		//		echo "forcer actualisation : ".date('H:i:s')."\n";
		//		foreach ($eqLogics as $eqLogic) {}
		//			$eqLogics[0]->executeTelnet();
			}
	} else {
			$telnet->telnetDisconnect();
			log::add('Optoma_Daemon', 'error','L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Erreur du démon : ' . $errstr . '(' . $errno . ')' );
			Optoma::deamon_stop();
	}