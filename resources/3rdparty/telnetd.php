#!/usr/
<?php

#$debug=1;
require_once dirname(__FILE__) . '/../core/class/vp_telnet.class.php';

$listen = config::byKey('listenport','vp_telnet');
$ipadr = config::byKey('ip','vp_telnet');

$telnet = new vp_telnet_telnet();
$errno = '';
$errstr = '';

echo 'Activation du daemon vp_telnet PARAMS :  ' . $errstr . '(IP : '.$ipadr.'  port : '.$listen.') '.date('H:i:s')."\n";

$refreshtime=60;
$delai=time();
	if($telnet->telnetConnect($ipadr,$listen,  $errno, $errstr)) {
		while(true) {
			$telnet->telnetGetReadResponse($result);
			$state='';
			$info='';
			echo "Résultat 1 = $result\n";
			echo "1 : ". $errstr . $errno."\n" ;
	$telnet->telnetSendCommand('~01150 1',$result);
                        echo "Résultat 2 = $result\n";
                        echo "2 : ". $errstr . $errno ."\n" ;
                        $telnet->telnetGetReadResponse($result);
                        echo "Résultat 3 = $result\n";
                        echo "3 : ". $errstr . $errno. "\n" ;

		}
		usleep(500);
		if( (time() - $delai) > $refreshtime )
			{
	} else {
			$telnet->telnetDisconnect();
			echo 'Daemon ERROR ' . $errstr . '(' . $errno . ')'."\n";
	}
      }

?>
