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


class Optoma extends eqLogic
{
    /*     * *************************Attributs****************************** */
    public static $_pluginVersion = '0.91';

    /*     * ***********************Methode static*************************** */

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
    

    public function preInsert()
    {
        $this->setCategory('multimedia', 1);
        $this->setIsEnable(1);
        $this->setIsVisible(1);
    }

    public function postInsert()
    {
        if ($this->getIsEnable()) {
            $this->loadCmdFromConf('UHD');
        }
    }

    public function postSave()
    {
    }

    public function preSave()
    {
    }

    public function preUpdate()
    {
        if (empty($this->getConfiguration('IP'))) {
            throw new Exception(__('L\'adresse IP ne peut pas être vide', __FILE__));
        }

        if ($this->getConfiguration('askCGI') == 1) {
            if (empty($this->getConfiguration('API'))) {
                throw new Exception(__('Le lien CGI ne peut pas être vide', __FILE__));
            }
            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->getConfiguration('API'))) {
                throw new Exception(__('Le format du lien CGI est incorrect', __FILE__));
            }
        }
    }

    public function postUpdate()
    {
        if ($this->getIsEnable()) {
            $this->loadCmdFromConf('UHD');
        }
    }

    public function getInfoUrl($_ip)
    {
        return "http://" . $_ip . "/Info.asp";
    }
  
    public function getControlUrl($_ip)
    {
        return "http://" . $_ip . "/Control.asp";
    }

    public function getAPIUrl($_ip, $_api)
    {
        return "http://" . $_ip . $_api;
    }

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
            if ($command['subtype'] == 'numeric') {
                $range = Optomapi::getRangeValue($command['logicalId']);
            } elseif ($command['subtype'] == 'slider') {
                $range = Optomapi::getRangeValue($command['configuration']['cmdInfo']);
                //if ($listValue != '') {
                 //   $cmd->setConfiguration('listValue', substr($listValue, 0, -1));
                //}
            }
            if (is_array($range)) {
                $cmd->setConfiguration('minValue', $range[0]);
                $cmd->setConfiguration('maxValue', $range[1]);
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
      
    public static function testCurl()
    {
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://192.168.1.47/tgi/login.tgi',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'Challenge=&Password=&Response=c95158ba1da9865d4b763367caad4026&Username=1&user=0',
  CURLOPT_HTTPHEADER => array(
    'Upgrade-Insecure-Requests: 1',
    'Content-Type: application/x-www-form-urlencoded',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36 Edg/89.0.774.50',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
        log::add('Optoma', 'debug', 'DEBUG testCurl résultat : ' . $response);
    }
  
function curl_post_test($url, $post="", $cookiejar="")
{
	$retstr = "";
	
	// output buffer b/c curl goes straight to screen
	ob_start();
	
	// create a new curl resource
	$ch = curl_init();
	
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	
	// post if applicable
	if (!empty($post))
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	} // end if post
	
	// handle cookies
	if (!empty($cookiejar))
	{
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
	} // end if cookiejar
	
	// grab URL and pass it to the browser
	curl_exec($ch);
	
	// close curl resource, and free up system resources
	curl_close($ch);
	
	$retstr = ob_get_clean();
	return $retstr;
} // end curl_post();
  
    public static function testLoginBis($_url, $_page, $_pwd)
    {
        $session = "";
        $login = self::curl_post_test($_url."/login.htm", "", "cookiejar");
        preg_match('/Challenge" VALUE="(\S+?)"/', $login, $matches);
        $challenge = $matches[1];
        $resp = md5("admin".$_pwd . $challenge);
        //$logincgi = self::curl_post_test($_url."/tgi/login.tgi", "Username=1&Password=".$_pwd."&Challenge=&Response=$resp", "cookiejar");
		self::testCurl();

        $portstats = self::curl_post_test($_url."/tgi/control.tgi", "", "cookiejar");
        $portstat3 = self::curl_post_test($_url."/tgi/control.tgi?Challenge=&Password=&Response=c95158ba1da9865d4b763367caad4026&Username=1&user=0", "", "cookiejar");
        log::add('Optoma', 'debug', 'DEBUG testLoginBis résultat1 : ' . $challenge);


        log::add('Optoma', 'debug', 'DEBUG testLoginBis résultat2 : ' . $logincgi);

        log::add('Optoma', 'debug', 'DEBUG testLoginBis résultat3 : ' . $portstats);
        log::add('Optoma', 'debug', 'DEBUG testLoginBis résultat4 : ' . $portstat3);

    }
  
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

    public static function amxDeviceDiscovery($_state)
    {
        log::add(__CLASS__, 'debug', "Lancement du mode inclusion.");
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
                self::DecodeAMXMessage($remote_ip, $buf);
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

    public static function DecodeAMXMessage($remote_ip, $buf)
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
            Optoma::AddEquipement($result, $remote_ip);
        }
    }

    public static function AddEquipement($_data, $_IP)
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

    public static function devicesParameters($filename)
    {
        $ModelVP = "UHD";
        $return = array();
        $path = dirname(__FILE__) . '/../config/'. $ModelVP . '/';
        log::add(__CLASS__, 'debug', __("Action sur ", __FILE__) . $path . $filename);

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
                    if ($eqLogic->getConfiguration('API') == "/tgi/control.tgi") {

                        log::add('Optoma', 'debug', "## TEST Connexion Bis tgi/control.tgi = ");
                        Optoma::testLoginBis($eqLogic->getConfiguration('IP'), $eqLogic->getConfiguration('API'), $eqLogic->getConfiguration('password'));
                    }
                    if ($this->getLogicalId() == 'Refresh') {
                        $result_api = $eqLogic->sendRequest($API_url);
                        preg_match('#{(.*)}#U', $result_api, $result);
                        $decodedResult = json_decode(preg_replace('/([{,])(\s*)([A-Za-z0-9_\-]+?)\s*:/', '$1"$3":', $result[0]), true);
                        $API = new Optomapi();
                        $full = $API->setFullNames($decodedResult);
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
?>
