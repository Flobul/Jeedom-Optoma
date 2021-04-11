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
require_once __DIR__ . "/../../../../core/php/core.inc.php";
require_once __DIR__ . "/../../../../plugins/Optoma/core/class/OptomaApi.class.php";
require_once __DIR__ . "/../../../../plugins/Optoma/core/class/OptomaRs232.class.php";
require_once __DIR__ . "/../../../../plugins/Optoma/core/class/OptomaCrestron.class.php";
require_once __DIR__ . "/../../../../plugins/Optoma/3rdparty/telnet.php";

class Optoma extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '0.95';

    /*     * ***********************Methode statique*************************** */

    /**
     * Lancement à l'intervalle selectionné de la commande Refresh
     * si l'équipement est actif et que la commande existe
     */
    public static function cron()
    {
        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' : début');
        $autorefresh = config::byKey('autorefresh', 'Optoma');
        $eqLogics = eqLogic::byType('Optoma');
        if ($autorefresh != '') {
            try {
                $cron = new Cron\CronExpression(checkAndFixCron($autorefresh), new Cron\FieldFactory);
                if ($cron->isDue()) {
                    try {
                        foreach ($eqLogics as $eqLogic) {
                            if ($eqLogic->getIsEnable()) {
                                $cmd = $eqLogic->getCmd(null, 'Refresh');
                                if (is_object($cmd)) {
                                    $cmd->execCmd();
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
        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' : fin');
    }

    public static function check_port($_IP)
    {
        $return = array();
        $ports = array(
            'TELNET' => 23,
            'API' => 80,
            'TELNET1' => 1023,
            'TELNET2' => 2023,
            'PJLINK' => 4352,
            'CRESTRON' => 41794
            );

        exec(system::getCmdSudo() . 'nc -zv ' . $_IP . ' -p ' . implode(' ', $ports) . ' 2>&1 > /dev/null', $res, $return_val);

        foreach ($res as $line) {
            foreach ($ports as $nom => $numero) {
                if (preg_match('/ '.$numero.'.* open/', $line, $match)) {
                    $return[$nom] = $numero;
                }
            }
        }
        return $return;
    }

    public static function check_API($_IP)
    {
        $APIs = ['/form/control_cgi','/tgi/control.tgi'];
        $result = '';
        foreach ($APIs as $API) {
            $API_url = self::getAPIUrl($_IP, $API);
            try {
                $request = new com_http($API_url);
            } catch (Exception $e) {
                log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . __(" Erreur d'authentification : ", __FILE__) . $request);
                break;
            }
            try {
                $result_api = $request->exec();
            } catch (Exception $e) {
                log::add(__CLASS__, 'debug', "L." . __LINE__ . " F." . __FUNCTION__ . __(" Erreur de connexion : ", __FILE__) . $e);
                break;
            }
            if (preg_match('#{(.*)}#U', $result_api, $res)) {
                $result = $API;
            }
        }
        return $result;
    }

    /**
     * Récupère les infos du démon dans les processus
     * @return array Etat du démon
     */
    public static function deamon_info()
    {
        //log::add('Optoma_Daemon', 'info', 'Etat du service Optoma');
        $return = array();
        $return['log'] = 'Optoma_Daemon';
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
     * Démarre le démon pendant 30 secondes, ou le redémarre si déjà démarré
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
        $listPorts = "";
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
        $ports = self::check_port($_IP);
        if (isset($ports['API'])) {
            $API = self::check_API($this->getConfiguration('IP'));
            if (isset($API)) {
                $this->setConfiguration('API', $API);
            }
            $Optoma->setConfiguration('actionMethod', 'API');
            $Optoma->setConfiguration('infoMethod', 'API');
        } elseif (isset($ports['TELNET1'])) {
            $Optoma->setConfiguration('actionMethod', 'TELNET1');
            $Optoma->setConfiguration('infoMethod', 'TELNET1');
            $Optoma->setConfiguration('telnetPort', $ports['TELNET1']);
        } elseif (isset($ports['TELNET2'])) {
            $Optoma->setConfiguration('actionMethod', 'TELNET');
            $Optoma->setConfiguration('infoMethod', 'TELNET');
            $Optoma->setConfiguration('telnetPort', $ports['TELNET2']);
        } elseif (isset($ports['CRESTRON'])) {
            $Optoma->setConfiguration('actionMethod', 'CRESTRON'); //default port: 41794
            //$Optoma->setConfiguration('infoMethod', $nom); // crestron info ne remonte pas
        }
        foreach ($ports as $nom => $port) {
            $listPorts .= $nom . " (" . $port . "),</br>";
        }
        $Optoma->setConfiguration('openPorts', substr($listPorts, 0, -6));
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
     * Vérifie les ports ouverts avant la sauvegarde de l'objet
     */
    public function preSave()
    {
        $listPorts = "";

        $ports = self::check_port($this->getConfiguration('IP'));

        foreach ($ports as $nom => $port) {
            $listPorts .= $nom . " (" . $port . "),</br>";
        }
        $this->setConfiguration('openPorts', substr($listPorts, 0, -6));

        $telnetPort = ($this->getConfiguration('telnetPort') != '') ? $this->getConfiguration('telnetPort') : 1023;
        if(in_array($this->getConfiguration('actionMethod'), array('TELNET','API-TELNET'))) {
            if ($this->getConfiguration('model') == '' || $this->getConfiguration('type') == '') {
                $telnet = new Optoma_telnet();
                try {
                    if ($telnet->telnetConnect($this->getConfiguration('IP'), $telnetPort, $errno, $errstr)) {
                        try {
                            $projectorID = ($this->getConfiguration('ID') != '') ? $this->getConfiguration('ID') : OptomaRs232::PROJECTOR_ID;
                            $readCmd = OptomaRs232::PREFIX . $projectorID . '151 1';

                            $telnet->telnetSendCommand($readCmd, $result);

                            log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " LogicalId result: " . $result);
                            if (!empty($result)) {
                                $reponse = explode("> ", $result);
                                if (count($reponse) == 1) {
                                    $value = $reponse[0];
                                } elseif (count($reponse) == 2) {
                                    $value = $reponse[1];
                                } elseif (count($reponse) == 3) {
                                    $value = $reponse[2];
                                } else {
                                    log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Detect inappropriate response !!! ');
                                }

                                switch (trim(substr($value, 0, 2))) {
                                    case 'F':
                                        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' '."RE (F) : FAIL ");
                                        break;
                                    case 'Ok':
                                    case 'OK':
                                        $value = OptomaRs232::getReadListValue('Model Name', trim(substr($value, 2, 1)));
                                }
                                if ($value != '' && strpos($value, 'Optoma') !== false)  {
                                    $modelType = explode(" ", $value);
                                    $this->setConfiguration('type', $modelType[0]);
                                    $this->setConfiguration('model', $modelType[1]);
                                }
                            }
                        } catch (Exception $exc) {
                            log::add('Optoma', 'debug', __("Erreur lors de l'exécution de la commande telnet ", __FILE__) . $exc->getMessage());
                        }
                    }
                } catch (Exception $exc) {
                    log::add('Optoma', 'debug', __("Erreur lors de la connexion telnet ", __FILE__) . $exc->getMessage());
                }
            }
        }
    }

    /**
     * Vérifie l'IP avant mise à jour de l'objet
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

            // attribuer les valeurs min et max des commandes numeric et slider
            if (isset($command['subtype']) && $command['subtype'] == 'numeric') {
                $range = Optomapi::getRangeValue($command['logicalId']);
            } elseif (isset($command['subtype']) && $command['subtype'] == 'slider') {
                $range = Optomapi::getRangeValue($command['configuration']['cmdInfo']);
                if (is_array($range)) {
                    $cmd->setConfiguration('minValue', $range[0]);
                    $cmd->setConfiguration('maxValue', $range[1]);
                }
            }

            // attribuer les listes de valeurs des commandes select
            if (isset($command['configuration']['cmdInfo']) && $command['subtype'] == 'select') {
                $listValue = Optomapi::getListValue($command['configuration']['cmdInfo']);
                if ($listValue != '') {
                    $cmd->setConfiguration('listValue', substr($listValue, 0, -1));
                }
            }

            $cmd->save();

            // lister les cmdInfo pour affecter l'id de la commande info à la commande action après création de l'équipement
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
    public function sendAPIRequest($_url, $_timeout = 2, $_retry = 5)
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
     * Rafraîchit toutes les commandes infos telnet
     * en récupérant la commande d'après le logicalId
     */
    public function getAllRs232Info()
    {
        $rawCmd = '';
        foreach ($this->getCmd('info') as $infoCmd) {
            $rawCmd = OptomaRs232::getRS232Command($infoCmd->getLogicalId());
            if (isset($rawCmd) && $rawCmd != '') {
                log::add(__CLASS__, 'debug', __("Lancement commande  ", __FILE__) . $infoCmd->getLogicalId() . " => " . $rawCmd);
                $port = ($this->getConfiguration('telnetPort') != '') ? $this->getConfiguration('telnetPort') : 1023;
                $telnet = new Optoma_telnet();
                try {
                    if ($telnet->telnetConnect($this->getConfiguration('IP'), $port, $errno, $errstr)) {
                        try {
                            $this->sendRS232RequestCommand($infoCmd, $telnet, $rawCmd);
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

    /**
     * Rafraîchit les commandes info telnet
     * en récupérant la commande dans le fichier de configration (heures lampe)
     */
    public function getMissingRs232Info()
    {
        foreach ($this->getCmd('info') as $infoCmd) {
            if ($infoCmd->getConfiguration('telnetCmd') != '') {
                $port = ($this->getConfiguration('telnetPort') != '') ? $this->getConfiguration('telnetPort') : 1023;
                $telnet = new Optoma_telnet();
                try {
                    if ($telnet->telnetConnect($this->getConfiguration('IP'), $port, $errno, $errstr)) {
                        try {
                            $this->sendRS232RequestCommand($infoCmd, $telnet);
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

    /**
     * Envoi la commande contenue dans la config de la commande info
     * en telnet sur le port de la config
     * @param  object $cmd    Commande info demandant màj des infos
     * @param  object $telnet Connexion telnet ouverte
     * @param  boolean $rawCmd Commande brute si getAllRs232Info
     */
    public function sendRS232RequestCommand($cmd, $telnet, $rawCmd = false)
    {
        $result = '';
        $delay = ($this->getConfiguration('waitdelay') != '') ? $this->getConfiguration('waitdelay') : OptomaRs232::WAIT_DELAY;
        $ordre = ($cmd->getConfiguration('telnetCmd') != '') ? $cmd->getConfiguration('telnetCmd') : $rawCmd; //Lamp total : 108 1  //Lamp bright : 108 3 //Lamp Eco : 108 4
        $projectorID = ($cmd->getConfiguration('ID') != '') ? $cmd->getConfiguration('ID') : OptomaRs232::PROJECTOR_ID;
        $readCmd = OptomaRs232::PREFIX . $projectorID . $ordre;
        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " LogicalId : " . $cmd->getLogicalId() . " Cmd : " . $readCmd);
        $telnet->telnetSendCommand($readCmd, $result);
        usleep($delay * 2000);
        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " LogicalId result: " . $result);

        if (!empty($result)) {
            $reponse = explode("> ", $result);
            if (count($reponse) == 1) {
                $value = $reponse[0];
            } elseif (count($reponse) == 2) {
                $value = $reponse[1];
            } elseif (count($reponse) == 3) {
                $value = $reponse[2];
            } else {
                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' Detect inappropriate response !!! ');
            }

            switch (trim(substr($value, 0, 2))) {
                    case 'F':
                        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' '."RE (F) : FAIL ");
                        break;
                    case 'Ok':
                    case 'OK':
                        $value = trim($value);
                        // Lamp hours en 5 digit numeric
                        if (in_array($cmd->getLogicalId(), array('Lamp Hours Bright','Lamp Hours Eco','Lamp Hours Dynamic','Lamp Hours Eco+','Lamp Hours Total','Lamp Hours Lamp 2 Hour'))) {
                            if (strlen(trim($value)) == 7) {
                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " Correct string : ". $value);
                                $value = substr($value, 2, 5);
                                if (is_numeric($value)) {
                                    $cmd->event($value);
                                    $cmd->save();
                                    log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' '. $cmd->getLogicalId() . ' = '.$value);
                                }
                            }
                            // commandes binaires
                        } elseif (in_array($cmd->getLogicalId(), array('AV Mute','Mute','Power','Powerstatus','Power Mode','Output 3D state','LAN DHCP','WLAN Network Status','LAN Network Status'))) {
                            if (strlen(trim($value)) == 3 || strlen(trim($value)) == 4) {
                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " Correct string : ". $value);
                                $value = substr($value, 2, 1);
                                if (is_numeric($value)) {
                                    $cmd->event($value);
                                    $cmd->save();
                                    log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' '. $cmd->getLogicalId() . ' = '.$value);
                                }
                            }
                            // commandes à valeur numériques
                        } elseif (in_array($cmd->getLogicalId(), array('Brightness','Contrast','Sharpness','Color','Tint','Freqency','Brilliant Color','Phase','H. Keystone','V. Keystone','H.Image Shift','V.Image Shift','Sleep Timer','Projector ID','Remote Code','Volume Micro','Treble','Bass'))) {
                            if (strlen(trim($value)) == 3 || strlen(trim($value)) == 4) {
                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " Correct string : ". $value);
                                $value = substr($value, 2, 2);
                                if (is_numeric(trim($value))) {
                                    $cmd->event($value);
                                    $cmd->save();
                                    log::add(__CLASS__, 'debug', __FUNCTION__.' L'.__LINE__.' '. $cmd->getLogicalId() . ' = '.$value);
                                }
                            }
                            // commande Aspect ratio uniquement
                        } elseif (in_array($cmd->getLogicalId(), array('Aspect Ratio','Color Temperature','Display Mode','Projection','Source'))) {
                            if (strlen(trim($value)) == 3 || strlen(trim($value)) == 4) {
                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " Correct string : ". $value);
                                $value = substr($value, 2, 2);
                                if (is_numeric($value)) {
                                    $value = OptomaRs232::getReadListValue($cmd->getLogicalId(), $value);
                                    if ($value != '') {
                                        $cmd->event($value);
                                        $cmd->save();
                                        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' ' . $cmd->getLogicalId() . ' = '.$value);
                                    }
                                }
                            }
                            // commande Info String + commandes 'Powerstatus', 'Source', 'Firmware Version', 'Display Mode'
                        } elseif ($cmd->getLogicalId() == "Info String") {
                            if (strlen(trim($value)) == 16) {
                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " Correct string : ". $value);
                                $value = substr($value, 2, 14);
                                if ($value != '') {
                                    $cmd->event(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $value));
                                    $cmd->save();
                                    log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' ' . $cmd->getLogicalId() . ' = '.$value);

                                    $infoString = OptomaRs232::getInformations($value);
                                    if (is_array($infoString)) {
                                        foreach ($infoString as $logId => $val) {
                                            $cmdToUpdate = $this->getCmd('info', $logId);
                                            if (is_object($cmdToUpdate)) {
                                                $cmdToUpdate->event($val);
                                                $cmdToUpdate->save();
                                                log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' ' . $cmdToUpdate->getLogicalId() . ' = '.$val);
                                            }
                                        }
                                    }
                                    log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . ' ' . $cmd->getLogicalId() . ' = '.json_encode($infoString));
                                }
                            }
                        }
                        break;
                    default:
                        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " RE (else) : ".$value);
                }
        }
    }

    /**
     * Envoie la commande action en Crestron
     * en récupérant la commande d'après le logicalId
     * @param  object $cmd Objet de la commande envoyée
     */
    public function sendCrestronCommand($_cmd)
    {
        $port = OptomaCrestron::CRESTRON_PORT;
        $resp = '';
        $telnet = new Optoma_telnet();
        try {
            if ($telnet->telnetConnect("tcp://" . $this->getConfiguration('IP'), $port, $errno, $errstr)) {
                try {
                    fwrite($telnet->fp, $_cmd);
                    $telnet->telnetGetReadResponse($resp);
                    $telnet->telnetDisconnect();
                } catch (Exception $exc) {
                    log::add(__CLASS__, 'debug', __("Erreur lors de l'exécution de la commande telnet ", __FILE__) . $exc->getMessage());
                }
            }
        } catch (Exception $exc) {
            log::add(__CLASS__, 'debug', __("Erreur lors de la connexion telnet ", __FILE__) . $exc->getMessage());
        }
        log::add(__CLASS__, 'debug', 'L.' . __LINE__ . ' F.' . __FUNCTION__ . " if OK " .$resp);
    }

    public function toHtml($_version = 'dashboard') {

        if ($this->getConfiguration('widgetTemplate') != 1) {
            return parent::toHtml($_version);
        }
        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            return $replace;
        }
		$_version = jeedom::versionAlias($_version);

        // informations de l'equipement 
        $replace['#device_type#'] = ($this->getConfiguration('type') != '') ? $this->getConfiguration('model') : "N/A";
        $replace['#device_model#'] = ($this->getConfiguration('model') != '') ? $this->getConfiguration('model') : "N/A";
        $replace['#info_method#'] = ($this->getConfiguration('infoMethod') != '') ? $this->getConfiguration('infoMethod') : "N/A";
        $replace['#action_method#'] = ($this->getConfiguration('actionMethod') != '') ? $this->getConfiguration('actionMethod') : "N/A";

		// POWER
		$powerStatus = $this->getCmd('info', 'Powerstatus');
		$replace['#power_status#'] = (is_object($powerStatus)) ? $powerStatus->execCmd() : '';
		$replace['#collect_power#'] = (is_object($powerStatus)) ? $powerStatus->getCollectDate() : '';

		$powerOn = $this->getCmd('action', 'Powerstatus::btn_powon');
		$replace['#power_on_id#'] = (is_object($powerOn)) ? $powerOn->getId() : '';
		$powerOff = $this->getCmd('action', 'Powerstatus::btn_powoff');
		$replace['#power_off_id#'] = (is_object($powerOff)) ? $powerOff->getId() : '';

		// INPUT
		$inputSource = $this->getCmd('info', 'Source');
		$replace['#input_source#'] = (is_object($inputSource)) ? $inputSource->execCmd() : '';

		$setInputSource = $this->getCmd('action', 'Source::');
		$replace['#setinput_source_id#'] = (is_object($setInputSource)) ? $setInputSource->getId() : '';
		$replace['#setinput_source_listvalue#'] = (is_object($setInputSource)) ? $setInputSource->getConfiguration('listValue') : '';

		// VOLUME
		$volume = $this->getCmd('info', 'Volume Audio');
		$replace['#volume#'] = (is_object($volume)) ? $volume->execCmd() : '';

        $volumeUp = $this->getCmd('action', 'Volume Audio::+');
		$replace['#volumeup_id#'] = (is_object($volumeUp)) ? $volumeUp->getId() : '';
		$volumeDown = $this->getCmd('action', 'Volume Audio::-');
		$replace['#volumedown_id#'] = (is_object($volumeDown)) ? $volumeDown->getId() : '';

		//MUTE
		//$MuteStatus = $this->getCmd(null,'MuteStatus');
		//$replace['#mute_status#'] = (is_object($MuteStatus)) ? $MuteStatus->execCmd() : '';
		//$replace['#collect_mute#'] = (is_object($PowerStatus)) ? $MuteStatus->getCollectDate() : '';
		$MuteOn = $this->getCmd(null,'MuteOn');
		$replace['#id_mute_on#'] = (is_object($MuteOn)) ? $MuteOn->getId() : '';
		$MuteOff = $this->getCmd(null,'MuteOff');
		$replace['#id_mute_off#'] = (is_object($MuteOff)) ? $MuteOff->getId() : '';

      //test fonction
/*		foreach($this->getCmd('action') as $val) {
			if ($val->getConfiguration('group') == "input") {
				//log::add('vp_telnet', 'debug','DEBUGGGGGG CMD ' . $val->getConfiguration('group'));
				foreach($this->getConfiguration('inputs') as $key => $value) {
                  if (str_replace("/","",$value) == str_replace("/","",$val->getName())) {
					log::add('vp_telnet', 'debug','DEBUGGGGGG INPUTS ' . $value . ' ' . $val->getName());
                  }
                }
      		}
		}*/

		$RefreshAction = $this->getCmd(null,'RefreshAction');
		$replace['#refresh_id#'] = (is_object($RefreshAction)) ? $RefreshAction->getId() : '';

		$html = template_replace($replace, getTemplate('core', $_version, 'Optoma.template',__CLASS__));

        return $html;
    }
}
  
class OptomaCmd extends cmd
{
    public function execute($_options = array())
    {
        $eqLogic = $this->getEqLogic();
        if ($this->getLogicalId() !== "") {
            $API_url = Optoma::getAPIUrl($eqLogic->getConfiguration('IP'), $eqLogic->getConfiguration('API'));
            if ($this->getLogicalId() == 'Refresh') {
                // selection de la méthode de récupération des infos
                if ($eqLogic->getConfiguration('infoMethod') == 'API' || $eqLogic->getConfiguration('infoMethod') == 'API-TELNET') {
                    //API get informations (all)
                    $result_api = $eqLogic->sendAPIRequest($API_url);
                    preg_match('#{(.*)}#U', $result_api, $result);
                    $decodedResult = json_decode(preg_replace('/([{,])(\s*)([A-Za-z0-9_\-]+?)\s*:/', '$1"$3":', $result[0]), true);
                    $API = new Optomapi();
                    $full = $API->setFullNames($decodedResult);
                    log::add('Optoma', 'debug', __('Valeurs API traduites ', __FILE__) . json_encode($full));
                    foreach ($full as $key => $value) {
                        $eqLogic->checkAndUpdateCmd($key, $value);
                    }
                    if ($eqLogic->getConfiguration('infoMethod') == 'API-TELNET') {
                        //RS232 get informations (hours)
                        $eqLogic->getMissingRs232Info();
                    }
                } elseif ($eqLogic->getConfiguration('infoMethod') == 'TELNET') {
                    //RS232 get informations (all)
                    $eqLogic->getAllRs232Info();
                }
            } else {
                if (strpos($this->getLogicalId(), "::") !== false) {
                    $args = explode("::", $this->getLogicalId());
                }
                if ($eqLogic->getConfiguration('actionMethod') == 'API' || $eqLogic->getConfiguration('actionMethod') == 'API-TELNET') {
                    switch ($this->getSubType()) {
                        case 'message':
                            $rawValueSelect = Optomapi::getSubtypeCmdFromLogicalId($args[0], 'select');
                            $value = $rawValueSelect . "=" . $_options['message'];
                            break;
                        case 'slider':
                            $rawIdSelect = Optomapi::getSubtypeCmdFromLogicalId($args[0], 'slider');
                            $value = $rawIdSelect . "=" . $_options['slider'];
                            break;
                        case 'select':
                            $rawIdSelect = Optomapi::getSubtypeCmdFromLogicalId($args[0], 'select');
                            $rawValueSelect = Optomapi::getIdFromValue($args[0], $_options['select']);
                            $value = $rawIdSelect . "=" . $rawValueSelect;
                            break;
                        case 'other':
                            if ($args[1] != '') {
                                $rawIdSelect = Optomapi::getSubtypeCmdFromLogicalId($args[0], $args[1]);
                                $value = $rawIdSelect . "=" . $args[1];
                            } else {
                                $rawValueSelect = Optomapi::getCmdFromLogicalId($args[0]);
                                $value = $args[0] . "=" . $rawValueSelect;
                            }
                    }
                    if ($value != '') {
                    	log::add('Optoma', 'debug', __FUNCTION__ . " RAW COMMANDE API === " . $value);
                        $result_api = $eqLogic->sendAPIRequest($API_url . '?' . urlencode($value));
                    }
                } elseif ($eqLogic->getConfiguration('actionMethod') == 'TELNET') {
                    switch ($this->getSubType()) {
                        case 'message':
                            $rawValueSelect = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], 'select');
                            $value = $rawValueSelect . " " . $_options['message'];
                            break;
                        case 'slider':
                            $command = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], 'slider');
                            $value = $command . " " . $_options['slider'];
                            break;
                        case 'select':
                            $rawIdSelect = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], 'select');
                            $rawValueSelect = OptomaRs232::getIdFromValue($args[0], $_options['select']);
                            $value = $rawIdSelect . " " . $rawValueSelect;
                            break;
                        case 'other':
                            if ($args[1] != '') {
                                if (in_array($args[1], array('-','+'))) {
                                    $rawIdSelect = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], $args[1]);
                                    $value = $rawIdSelect;
                                } else {
                                    $rawIdSelect = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], 'other');
                                    $rawValueSelect = OptomaRs232::getIdFromValue($args[0], $args[1]);
                                    $value = $rawIdSelect . " " . $rawValueSelect;
                                }
                            } else {
                                $rawIdSelect = OptomaRs232::getSubtypeCmdFromLogicalId($args[0], 'other');
                                $value = $rawIdSelect;
                            }
                    }
                    if ($value != '') {
                    	log::add('Optoma', 'debug', __FUNCTION__ . " RAW COMMANDE TELNET === " . $value);
                        $telnet = new Optoma_telnet();
                        try {
                            if ($telnet->telnetConnect($this->getConfiguration('IP'), $port, $errno, $errstr)) {
                                try {
                                    $this->sendRS232RequestCommand($this, $telnet, $value);
                                } catch (Exception $exc) {
                                    log::add('Optoma', 'debug', __("Erreur lors de l'exécution de la commande telnet ", __FILE__) . $exc->getMessage());
                                }
                            }
                        } catch (Exception $exc) {
                            log::add('Optoma', 'debug', __("Erreur lors de la connexion telnet ", __FILE__) . $exc->getMessage());
                        }
                    }
                } elseif ($eqLogic->getConfiguration('actionMethod') == 'CRESTRON') {
                    switch ($this->getSubType()) {
                        case 'message':
                            break;
                        case 'slider':

                            break;
                        case 'select':

                            break;
                        case 'other':
                            if ($args[1] != '') {
                                $value = OptomaCrestron::getSubtypeCmdFromLogicalId($args[0], $args[1]);
                            } else {
                                $value = OptomaCrestron::getSubtypeCmdFromLogicalId($args[0], 'other');
                            }
                    }
                    if ($value != '') {
                        log::add('Optoma', 'debug', __FUNCTION__ . " RAW COMMANDE CRESTRON === " . $value);
                        $eqLogic->sendCrestronCommand(OptomaCrestron::DIGITAL_JOIN . $rawIdSelect);
                    }
                }
            }
        }
        log::add('Optoma', 'debug', __("Action sur ", __FILE__) . $this->getLogicalId());
    }
}