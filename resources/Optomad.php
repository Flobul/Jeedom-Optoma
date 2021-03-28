#!/usr/
<?php

#$debug=1;
// require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../core/class/Optoma.class.php';
require_once dirname(__FILE__) . '/../core/class/Optomapi.class.php';
require_once dirname(__FILE__) . '/../core/class/Optomars232.class.php';

log::add('Optoma_Daemon', 'info', __('Activation du service Optoma', __FILE__));

$listen = config::byKey('listenport', 'Optoma');
$ipadr = config::byKey('ip', 'Optoma');

$telnet = new Optoma_telnet();
$errno = '';
$errstr = '';

log::add('Optoma_Daemon', 'debug', __('Activation du daemon Optoma sur l\'IP ', __FILE__).$ipadr.' port : '.$listen);

$eqLogics = eqLogic::byType('Optoma');
$refreshtime = 60;
$delain = time();
    $connect = $telnet->telnetConnect($ipadr, $listen, $errno, $errstr);
    if ($connect) {
        while (true) {
            $telnet->telnetGetReadResponse($result);
            $state='';

            if (preg_match('/[INFO]+([0-9]+)/i', $result, $matches)) {
                $state = Optomars232::getError($matches[1]);
            } elseif (trim($result) != '') {
                log::add('Optoma_Daemon', 'info', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . __(' Nouvelle information non implémentée : ', __FILE__) . json_encode($result));
            }
            if ($state != '') {
                foreach ($eqLogics as $eqLogic) {
                    if ($eqLogic->getConfiguration('IP') == config::byKey('ip', 'Optoma')) {
                        log::add('Optoma_Daemon', 'info', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . __(' Vidéoprojecteur trouvé, mise à jour de la valeur de SystemInfo', __FILE__));
                        $systemInfo = $eqLogic->getCmd(null, 'SystemInfo');
                        if (is_object($systemInfo)) {
                            $systemInfo->event($state);
                            log::add('Optoma_Daemon', 'info', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . __(' Valeur de SystemInfo mise à jour ', __FILE__) . $state);
                        }
                    }
                }
            }
        }
        usleep(500);
        if ((time() - $delai) > $refreshtime) {
            // forcer actualisation
        //		echo "forcer actualisation : ".date('H:i:s')."\n";
        //		foreach ($eqLogics as $eqLogic) {}
        //			$eqLogics[0]->executeTelnet();
        }
    } else {
        $telnet->telnetDisconnect();
        log::add('Optoma_Daemon', 'error', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . __(' Erreur du démon : ', __FILE__) . $errstr . '(' . $errno . ')');
        Optoma::deamon_stop();
    }
