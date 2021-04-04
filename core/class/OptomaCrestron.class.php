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

class OptomaCrestron
{
    /*     * *************************Attributs****************************** */

    const CRESTRON_PORT          = 41794;
    const ANALOG_JOIN            = "\x05\x00\x08\x00\x00\x05\x00";
    const DIGITAL_JOIN           = "\x05\x00\x06\x00\x00\x03\x00";
    const HEARTBEAT              = "\x13\x00\x02\x00\x00";
    const CONNECT_MESSAGE        = "\x01\x00\x07\x00\x00\x00\x00\x03\x03\x64";
    const END_OF_QUERY_RESPONSE  = "\x05\x00\x05\x00\x00\x02\x03\x29";
    const UPDATE_REQUEST         = "\x05\x00\x05\x00\x00\x02\x03\x30";

    /**
     * Tableau des logicalId et commandes action
     */
    private static $_sendCmd = array(
        'Powerstatus' => array(
            'btn_powon'  => "\x00\x05", //OK
            'btn_powoff' => "\x00\x06"  //OK
        ),
        'Volume Audio' => array(
            '+' => "\xfa\x13", //OK
            '-' => "\xfb\x13"  //OK
        ),
        'Mute' => array(
            'other' => "\xfd\x13" //OK
        ),
        'Mute Off' => array(
            'other' => "\xfc\x13" //OK
        ),
        'Freeze' => array(
            'other' => "\xf0\x13" //OK
        ),
        'Resync' => array(
            'other' => "\x33\x14" //OK
        ),
        'Color' => array(
            '+' => "\xf2\x13", // non fonctionnel
            '-' => "\xf3\x13"  // non fonctionnel
        ),
        'Brightness' => array(
            '+' => "\xf4\x13", // OK
            '-' => "\xf5\x13"  // OK
        ),
        'Contrast' => array(
            '+' => "\xf6\x13", // OK
            '-' => "\xf7\x13"  // OK
        ),
        'Sharpness' => array(
            '+' => "\xf8\x13", // OK
            '-' => "\xf9\x13"  // OK
        ),
        'Zoom' => array(
            '+' => "\x39\x14", // OK
            '-' => "\x3a\x14"  // OK
        ),
        'Menu' => array(
            'other' => "\x1d\x14" //OK
        ),
        'Up' => array(
            'other' => "\x1e\x14" //OK
        ),
        'Down' => array(
            'other' => "\x1f\x14" //OK
        ),
        'Left' => array(
            'other' => "\x20\x14" //OK
        ),
        'Right' => array(
            'other' => "\x21\x14" //OK
        ),
        'Exit' => array(
            'other' => "\x22\x14" //OK
        ),
        'Enter' => array(
            'other' => "\x23\x14" //OK
        ),
        'Source' => array(
            'other' => "\x6f\x17" //OK
        ),
        'Freeze Off' => array(
            'other' => "\xf1\x13" //non fonctionnel
        )
    );

    /*     * ***********************Methodes statiques*************************** */

    /**
     * Renvoie la commande crestron associée
     * @param  string $_name LogicalId de la commande
     * @return string $_name 4-bit Commande brute à envoyer
     */
    public static function getCrestronWriteCommand($_name)
    {
        $_name = (array_key_exists($_name, self::$_sendCmd) == true) ? self::$_sendCmd[$_name] : false;
        return $_name;
    }

    public static function getSubtypeCmdFromLogicalId($_key, $_subtype)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        $_value = '';
        if (array_key_exists($_key, self::$_sendCmd) == true) {
            $_value = self::$_sendCmd[$_key][$_subtype];
        }
        return $_value;
    }
}