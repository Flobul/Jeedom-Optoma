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
require_once __DIR__ . "/../../../../plugins/Optoma/core/class/Optomapi.class.php";
require_once __DIR__ . '/../../../../plugins/Optoma/3rdparty/telnet.php';

class Optoma extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '0.93';

    /*     * ***********************Methode statique*************************** */

    /**
     * Lancement à l'intervalle selectionné de la commande Refresh
     * si l'équipement est actif et que la commande existe
     */
    public static function cron()
    {
        $autorefresh = config::byKey('autorefresh', 'Optoma');
        $eqLogics = eqLogic::byType('Optoma');
        if ($autorefresh != '') {
            try {
                $cron = new Cron\CronExpression(checkAndFixCron($autorefresh), new Cron\FieldFactory);
                if ($cron->isDue()) {
                    try {
                        log::add(__CLASS__, 'debug', __("Démarrage du cron ", __FILE__). $autorefresh);
                        foreach ($eqLogics as $eqLogic) {
                            if ($eqLogic->getIsEnable()) {
                                $cmd = $eqLogic->getCmd(null, 'Refresh');
                                if (is_object($cmd)) {
                                    $cmd->execCmd();
                                }
                                foreach ($eqLogic->getCmd('info') as $infoCmd) {
                                    if ($infoCmd->getConfiguration('telnetCmd') != '') {
                                        $port = ($eqLogic->getConfiguration('telnetPort') != '') ? $eqLogic->getConfiguration('telnetPort') : 1023;
                                        $telnet = new Optoma_telnet();
                                        try {
                                            if ($telnet->telnetConnect($eqLogic->getConfiguration('IP'), $port, $errno, $errstr)) {
                                                try {
                                                    $eqLogic->sendCommand($infoCmd, $telnet);
                                                } catch (Exception $exc) {
                                                    log::add(__CLASS__, 'debug', __("Erreur lors de l'exécution de la commande telnet ", __FILE__) . $exc->getMessage());
                                                }
                                            }
                                        } catch (Exception $exc) {
                                            log::add(__CLASS__, 'debug', __("Erreur lors de la connexion telnet ", __FILE__) . $exc->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $exc) {
                        log::add(__CLASS__, 'debug', __("Erreur lors de l'exécution du cron ", __FILE__) . $exc->getMessage());
                    }
                    log::add(__CLASS__, 'debug', __("Fin d'exécution du cron ", __FILE__). $autorefresh);
                }
            } catch (Exception $exc) {
                log::add(__CLASS__, 'error', __("Erreur lors de l'exécution du cron ", __FILE__) . $exc->getMessage());
            }
        }
        log::add(__CLASS__, 'debug', __FUNCTION__ . __(' : fin', __FILE__));
    }

    /**
     * Récupère les infos du démon dans les processus
     * @return array Etat du démon
     */
    public static function deamon_info()
    {
        //log::add('Optoma_Daemon', 'info', 'Etat du service Optoma');
        $return = array();
        $return['log'] = 'vp_telnet_Daemon';
        $return['state'] = 'nok';
        $pid = trim(shell_exec('ps ax | grep "/Optomad.php" | grep -v "grep" | wc -l'));
        if ($pid != '' && $pid != '0') {
            $return['state'] = 'ok';
        }
        if (config::byKey('listenport', 'Optoma') >  '1') {
            $return['launchable'] = 'ok';
        } else {
            $return['launchable'] = 'nok';
            $return['launchable_message'] = __('Le port n\'est pas configuré.', __FILE__);
        }
        //log::add('Optoma_Daemon', 'info', "Statut=".$return['state']);
        return $return;
    }

    /**
     * Démarre le démon pendant 3 secondes, ou le redémarre si déjà démarré
     * @param  boolean $_debug [description]
     * @return boolean         Vrai si OK, faux si erreur.
     */
    public static function deamon_start($_debug = false)
    {
        log::add('Optoma_Daemon', 'info', __('Lancement du service Optoma', __FILE__));
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        if ($deamon_info['state'] == 'ok') {
            self::deamon_stop();
            sleep(2);
        }
        log::add('Optoma_Daemon', 'info', __('Lancement du démon Optoma', __FILE__));
        $cmd = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), '/core/class')).'/resources/Optomad.php';

        $result = exec('sudo php ' . $cmd . ' >> ' . log::getPathToLog('Optoma_Daemon') . ' 2>&1 &');
        if (strpos(strtolower($result), 'error') !== false || strpos(strtolower($result), 'traceback') !== false) {
            log::add('Optoma_Daemon', 'error', $result);
            return false;
        }
        sleep(1);
        $i = 0;
        while ($i < 30) {
            $deamon_info = self::deamon_info();
            if ($deamon_info['state'] == 'ok') {
                break;
            }
            sleep(1);
            $i++;
        }
        if ($i >= 30) {
            log::add('Optoma_Daemon', 'error', __('Impossible de lancer le démon Optoma_Daemon', __FILE__), 'unableStartDeamon');
            return false;
        }
        log::add('Optoma_Daemon', 'info', __('Démon Optoma_Daemon lancé', __FILE__));
        return true;
    }

    /**
     * Arrête le démon
     * @return boolean Vrai si arrêté
     */
    public static function deamon_stop()
    {
        log::add('Optoma_Daemon', 'info', __('Arrêt du service Optoma', __FILE__));
        $cmd='/Optomad.php';
        exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
        sleep(1);
        exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
        sleep(1);
        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok') {
            exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
            sleep(1);
        } else {
            return true;
        }
        $deamon_info = self::deamon_info();
        if ($deamon_info['state'] == 'ok') {
            exec('sudo kill -9 $(ps aux | grep "'.$cmd.'" | awk \'{print $2}\')');
            sleep(1);
            return true;
        }
    }

    /**
     * Ouvre un socket pendant 60 secondes et envoie le résultat en buffer à décoder
     * @param  bool $_state Etat du mode inclusion
     */
    public static function amxDeviceDiscovery($_state)
    {
        log::add(__CLASS__, 'debug', __("Lancement du mode inclusion", __FILE__));
        if ($_state == 1) {
            event::add('Optoma::includeDevice', null);
            if (!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))) {
                log::add(__CLASS__, 'debug', "Couldn't create socket: " . socket_strerror(socket_last_error($sock)));
                return false;
            }
            if (!socket_bind($sock, "0.0.0.0", 9131)) {
                log::add(__CLASS__, 'debug', "Couldn't bind port: " . socket_strerror(socket_last_error($sock)));
                return false;
            }
            if (!socket_set_option($sock, IPPROTO_IP, MCAST_JOIN_GROUP, array("group"=>"239.255.250.250","interface"=>0))) {
                log::add(__CLASS__, 'debug', "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($sock)));
                return false;
            }
            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>60, "usec"=>0));
            $start=time();
            while (true) {
                $r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
                log::add(__CLASS__, 'debug', $remote_ip." : ".$remote_port." -- " . $buf);
                self::decodeAMXMessage($remote_ip, $buf);
                if ((time()-$start) > 60) {
                    break;
                }
            }
            socket_close($sock);
            event::add('Optoma::includeDevice', null);
        } else {
            log::add(__CLASS__, 'debug', __("Fin manuelle de l'inclusion", __FILE__));
        }
    }

    /**
     * Décode le message reçu par le socket (AMX Device Discovery)
     * et créé l'équipement avec le message reçu
     * @param  string $remote_ip IP relevée à la réception du buffer
     * @param  string $buf       Message reçu contenant les infos de l'appareil
     */
    public static function decodeAMXMessage($remote_ip, $buf)
    {
        $result = array();
        foreach (explode('<-', str_replace('>', '', $buf)) as $param) {
            $Make = explode('=', $param);
            if ($Make[0] == "Make") {
                $result['type'] = str_replace(' ', '_', $Make[1]);
                if ($result['type'] !== "Optoma") {
                    log::add(__CLASS__, 'debug', __("Ce n'est pas un Optoma: ", __FILE__) . $result['type']);
                }
            }
            if ($Make[0] == "Model") {
                $result['model'] = str_replace(' ', '_', $Make[1]);
            }
            if ($Make[0] == "UUID") {
                $result['UUID'] = str_replace(' ', '_', $Make[1]);
            }
            if ($Make[0] == "SDKClass") {
                $result['SDKClass'] = str_replace(' ', '_', $Make[1]);
                if ($result['SDKClass'] !== "VideoProjector") {
                    log::add(__CLASS__, 'debug', __("Ce n'est pas un vidéoprojecteur: ", __FILE__) . $result['SDKClass']);
                }
            }
        }
        if ($result['SDKClass'] == 'VideoProjector') {
            Optoma::addEquipement($result, $remote_ip);
        }
    }

    /**
     * Créé l'équipement avec les valeurs du buffer
     * @param array $_data Tableau des valeurs récupérées dans le buffer
     * @param string $_IP   IP relevée à la réception du buffer
     * @return object $Optoma Retourne l'équipement créé
     */
    public static function addEquipement($_data, $_IP)
    {
        $name = $_data['type'] . " " . $_data['model'] . " - " . $_data['UUID'];
        foreach (self::byLogicalId($_data['UUID'], 'Optoma', true) as $Optoma) {
            if (is_object($Optoma) && $Optoma->getConfiguration('IP') == $_IP) {
                return $Optoma;
            }
        }

        $Optoma = new Optoma();
        $Optoma->setName($name);
        $Optoma->setLogicalId($_data['UUID']);
        $Optoma->setObject_id(null);
        $Optoma->setEqType_name('Optoma');
        $Optoma->setIsEnable(1);
        $Optoma->setIsVisible(1);
        $Optoma->setConfiguration('type', $_data['type']);
        $Optoma->setConfiguration('model', $_data['model']);
        $Optoma->setConfiguration('MAC', $_data['UUID']);
        $Optoma->setConfiguration('IP', $_IP);
        $Optoma->setConfiguration('auto_discovery', 'AMX Device Discovery');
        $Optoma->save();
        config::save('include_mode', 0, 'Optoma');
        event::add('Optoma::includeDevice', $Optoma->getId());

        return $Optoma;
    }

    /**
     * Recherche la configuration dans le dossier du modèle
     * et renvoie la configuration associée
     * @param  string $filename Nom du fichier
     * @return array           Configuration en tableau
     */
    public static function devicesParameters($filename)
    {
        $ModelVP = "UHD";
        $return = array();
        $path = dirname(__FILE__) . '/../config/'. $ModelVP . '/';

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

    /**
     * Active et affiche avant création de l'objet
     */
    public function preInsert()
    {
        $this->setCategory('multimedia', 1);
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    /**
     * Charge la liste des commandes après création de l'objet
     */
    public function postInsert()
    {
        if ($this->getIsEnable()) {
            $this->loadCmdFromConf('UHD');
        }
    }

    /**
     * Vérifie l'IP avant sauvegarde de l'objet
     */
    public function preUpdate()
    {
        if (empty($this->getConfiguration('IP'))) {
            throw new Exception(__('L\'adresse IP ne peut pas être vide', __FILE__));
        }
        if (!filter_var($this->getConfiguration('IP'), FILTER_VALIDATE_IP)) {
            throw new Exception(__('Le format de l\'adresse IP est incorrect', __FILE__));
        }
    }

    /**
     * Recharge les commandes après mise à jour de l'objet
     */
    public function postUpdate()
    {
        if ($this->getIsEnable()) {
            $this->loadCmdFromConf('UHD');
        }
    }

    /**
     * Retourne l'URL de la page Info
     * @param  string $_ip Adresse IP de l'équipement
     * @return string      URL de la page Info
     */
    public function getInfoUrl($_ip)
    {
        return "http://" . $_ip . "/Info.asp";
    }

    /**
     * Retourne l'URL de la page Control
     * @param  string $_ip Adresse IP de l'équipement
     * @return string      URL de la page Control
     */
    public function getControlUrl($_ip)
    {
        return "http://" . $_ip . "/Control.asp";
    }

    /**
     * Retourne l'URL de l'API
     * @param  string $_ip  Adresse IP de l'équipement
     * @param  string $_api API de l'équipement
     * @return string       URL de l'API
     */
    public function getAPIUrl($_ip, $_api)
    {
        return "http://" . $_ip . $_api;
    }

    /**
     * Recherche la configuration dans le dossier du modèle
     * et créé les commandes si elles sont inexistantes
     * @param  string $type Nom
     * @return boolean       Renvoi 0 si fichier en erreur, 1 sinon
     */
    public function loadCmdFromConf($type)
    {
        $return = array();
        if (!is_file(dirname(__FILE__) . '/../../core/config/devices/' . $type . '.json')) {
            log::add(__CLASS__, 'debug', __("Fichier introuvable : ", __FILE__) . dirname(__FILE__) . '/config/devices/' . $type . '.json');
            return false;
        }
        $content = file_get_contents(dirname(__FILE__) . '/../../core/config/devices/' . $type . '.json');
        if (!is_json($content)) {
            log::add(__CLASS__, 'debug', __("JSON invalide : ", __FILE__) . $type . '.json');
            return false;
        }
        $device = json_decode($content, true);
        if (!is_array($device) || !isset($device['commands'])) {
            log::add(__CLASS__, 'debug', __("Tableau incorrect : ", __FILE__) . $type . '.json');
            return false;
        }

        foreach ($device['commands'] as $command) {
            $cmd = null;
            foreach ($this->getCmd() as $liste_cmd) {
                if ((isset($command['logicalId']) && $liste_cmd->getLogicalId() == $command['logicalId']) || (isset($command['name']) && $liste_cmd->getName() == $command['name'])) {
                    $cmd = $liste_cmd;
                    break;
                }
            }

            if ($cmd == null || !is_object($cmd)) {
                $cmd = new OptomaCmd();
                $cmd->setEqLogic_id($this->getId());
            }
            utils::a2o($cmd, $command);
            if (isset($command['subtype']) && $command['subtype'] == 'numeric') {
                $range = Optomapi::getRangeValue($command['logicalId']);
            } elseif (isset($command['subtype']) && $command['subtype'] == 'slider') {
                $range = Optomapi::getRangeValue($command['configuration']['cmdInfo']);
                if (is_array($range)) {
                    $cmd->setConfiguration('minValue', $range[0]);
                    $cmd->setConfiguration('maxValue', $range[1]);
                }
            }

            if (isset($command['configuration']['cmdInfo']) && $command['subtype'] == 'select') {
                $listValue = Optomapi::getListValue($command['configuration']['cmdInfo']);
                if ($listValue != '') {
                    $cmd->setConfiguration('listValue', substr($listValue, 0, -1));
                }
            }
            $cmd->save();
            if (isset($command['configuration']) && isset($command['configuration']['cmdInfo'])) {
                $link_cmds[$cmd->getId()] = $command['configuration']['cmdInfo'];
            }
        }
        if (count($link_cmds) > 0) {
            foreach ($this->getCmd() as $eqLogic_cmd) {
                foreach ($link_cmds as $cmd_id => $link_cmd) {
                    if ($link_cmd == $eqLogic_cmd->getName()) {
                        $cmd = cmd::byId($cmd_id);
                        if (is_object($cmd)) {
                            $cmd->setConfiguration('updateCmdId', $eqLogic_cmd->getId());
                            $cmd->setValue($eqLogic_cmd->getId());
                            $cmd->save();
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Envoi des requêtes vers l'url et renvoi le résultat
     * @param  string  $_url     URL des données
     * @param  integer $_timeout Temps maximal de connexion
     * @param  integer $_retry   Nombre de tentatives de connexions
     * @return array            Résultat de la requête (json)
     */
    public function sendRequest($_url, $_timeout = 2, $_retry = 5)
    {
        try {
            $request = new com_http($_url, $this->getConfiguration('username'), $this->getConfiguration('password'));
        } catch (Exception $e) {
            log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . __(" Erreur d'authentification : ", __FILE__) . $request);
            return;
        }

        try {
            $result = $request->exec($_timeout, $_retry);
        } catch (Exception $e) {
            log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . __(" Erreur de connexion : ", __FILE__) . $e);
            return;
        }
        //log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . " résultat : " . $result);
        if (strpos($result, "<html>") !== false) {
            return $result;
        } else {
            log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . __(" Données non reconnues : ", __FILE__) . $result);
            return false;
        }
    }

    /**
     * Envoi la commande contenue dans la config de la commande info
     * en telnet sur le port de la config
     * @param  object $cmd    Commande info demandant màj des infos
     * @param  object $telnet Connexion telnet ouverte
     */
    public function sendCommand($cmd, $telnet)
    {
        $result = '';
        $prefixCmd = '~';
        $projectorID = '00'; //if (isset(cmd->execCmd()))

        $ordre = $cmd->getConfiguration('telnetCmd'); //Lamp total : 108 1  //Lamp bright : 108 3 //Lamp Eco : 108 4

        if (isset($ordre) && $ordre != '') {
            $readCmd = $prefixCmd . $projectorID . $ordre;
            log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '. "Cmd : " .$readCmd);

            $telnet->telnetSendCommand($readCmd, $result);
            //usleep($this->getConfiguration('waitdelay') * 1000);
            usleep(10 * 1000);

            if (!empty($result)) {
                log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '. ' RE : '.$result);
                $reponse=explode("> ", $result);
                if (count($reponse) == 1) {
                    $value=$reponse[0];
                } elseif (count($reponse) == 2) {
                    $value=$reponse[1];
                } elseif (count($reponse) == 3) {
                    $value=$reponse[2];
                } else {
                    log::add(__CLASS__, 'info', __FUNCTION__.' L'.__LINE__.' Detect inappropriate response !!! ');
                }

                switch (trim(substr($value, 0, 2))) {
                    case 'F':
                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE (F) : FAIL ");
                        break;
                    case 'Ok':
                    case 'OK':
                        $reponse=explode(" ", $value);
                        $value = $reponse[0];

                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE (Ok) : ".$reponse[1]);
                        $info = array();

                        if ($cmd->getName() == "LampTotal") {
                            switch (trim(substr($value, 0, 2))) {
                                case 'F':
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : FAIL ");
                                    break;
                                case 'Ok':
                                case 'OK':
                                    $reponse=explode(" ", $value);
                                    $value=$reponse[0];
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : ".$value);
                                    if (strlen(trim($value)) != 7) {
                                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Incorrect string : ". $value);
                                        $info['lampTotal']=substr($value, 3, 5);
                                        $updateCmd = $this->getCmd(null, 'LampTotal');
                                        if ((is_numeric($info['lampTotal'])) && is_object($updateCmd)) {
                                            $updateCmd->event($info['lampTotal']);
                                            $updateCmd->save();
                                            log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '.'lampTotal='.$info['lampTotal']);
                                        }
                                        unset($updateCmd);
                                    }
                                    break;
                                default:
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Error LampTotal");
                            }
                        } elseif ($cmd->getName() == "LampBright") {
                            switch (trim(substr($value, 0, 2))) {
                                case 'F':
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : FAIL ");
                                    break;
                                case 'Ok':
                                case 'OK':
                                    $reponse=explode(" ", $value);
                                    $value=$reponse[0];
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : ".$value);
                                    if (strlen(trim($value)) != 7) {
                                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Incorrect string : ". $value);
                                    } else {
                                        $info['LampBright']=substr($value, 3, 5);
                                        $updateCmd = $this->getCmd(null, 'LampBright');
                                        if ((is_numeric($info['LampBright'])) && is_object($updateCmd)) {
                                            $updateCmd->event($info['LampBright']);
                                            $updateCmd->save();
                                            log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '.'LampBright='.$info['LampBright']);
                                        }
                                        unset($updateCmd);
                                    }
                                    break;
                                default:
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Error LampBright");
                            }
                        } elseif ($cmd->getName() == "LampEco") {
                            switch (trim(substr($value, 0, 2))) {
                                case 'F':
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : FAIL ");
                                    break;
                                case 'Ok':
                                case 'OK':
                                    $reponse=explode(" ", $value);
                                    $value=$reponse[0];
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE : ".$value);
                                    if (strlen(trim($value)) != 7) {
                                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Incorrect string : ". $value);
                                        $info['LampEco']=substr($value, 3, 5);
                                        $updateCmd = $this->getCmd(null, 'LampEco');
                                        if ((is_numeric($value)) && is_object($updateCmd)) {
                                            $updateCmd->event($info['LampEco']);
                                            $updateCmd->save();
                                            log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '.'LampEco='.$info['LampEco']);
                                        }
                                        unset($updateCmd);
                                    }
                                    break;
                                default:
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."Error LampBright");
                            }
                        }
                            break;
                        default:
                        log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '."RE (else) : ".$value);
                }
            }
        }
    }
}

class OptomaCmd extends cmd
{
    public function execute($_options = array())
    {
        $eqLogic = $this->getEqLogic();
        if ($this->getLogicalId() !== "") {
            $API_url = Optoma::getAPIUrl($eqLogic->getConfiguration('IP'), $eqLogic->getConfiguration('API'));
            if (strpos($this->getLogicalId(), "::") !== false) {
                $args = explode("::", $this->getLogicalId());
            }
            switch ($this->getSubType()) {
                case 'message':
                    $value = $args[0] . "=" . $_options['message'];
                    break;
                case 'slider':
                    $value = $args[0] . "=" . $_options['slider'];
                    break;
                case 'select':
                    $select = Optomapi::getValueFromId($this->getConfiguration('cmdInfo'), $_options['select']);
                    $value = $args[0] . "=" . $select;
                    break;
                case 'other':
                    if ($this->getLogicalId() == 'Refresh') {
                        $result_api = $eqLogic->sendRequest($API_url);
                        preg_match('#{(.*)}#U', $result_api, $result);
                        $decodedResult = json_decode(preg_replace('/([{,])(\s*)([A-Za-z0-9_\-]+?)\s*:/', '$1"$3":', $result[0]), true);
                        $API = new Optomapi();
                        $full = $API->setFullNames($decodedResult);
                        log::add('Optoma', 'debug', __('Valeurs API traduites ', __FILE__) . json_encode($full));
                        foreach ($full as $key => $value) {
                            $eqLogic->checkAndUpdateCmd($key, $value);
                        }
                    } else {
                        $value = $args[0] . "=" . $args[1];
                    }
            }
            if ($this->getLogicalId() !== 'Refresh') {
                $result_api = $eqLogic->sendRequest($API_url . '?' . urlencode($value));
            }
        }
        log::add('Optoma', 'debug', __("Action sur ", __FILE__) . $this->getLogicalId());
    }
}
