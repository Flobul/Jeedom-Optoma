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

    const ANALOG_JOIN = "\x05\x00\x08\x00\x00\x05\x00";
    const DIGITAL__JOIN = "\x05\x00\x06\x00\x00\x03\x00";
    const UPDATE_REQUEST = "\x05\x00\x05\x00\x00\x02\x03\x00";

    /**
     * Tableau des logicalId et commandes action
     */
    private static $_writeCmd = array(
        'Power On'     => "\x00\x05", //OK
        'Power Off'    => "\x00\x06", //OK
        'Volume Up'    => "\xfa\x13", //OK
        'Volume Down'  => "\xfb\x13", //OK
        'Mute Off'     => "\xfc\x13", //OK
        'Mute On'      => "\xfd\x13", //OK
        'Menu'         => "\x1d\x14", //OK
        'Up'           => "\x1e\x14", //OK
        'Down'         => "\x1f\x14", //OK
        'Left'         => "\x20\x14", //OK
        'Right'        => "\x21\x14", //OK
        'Exit'         => "\x22\x14", //OK
        'Enter'        => "\x23\x14", //OK
        'Resync'       => "\x33\x14",
        'Source'       => "\x6f\x17", //OK
        'Freeze On'    => "\xf0\x13", //OK
        'Freeze Off'   => "\xf1\x13", //non fonctionnel
        'Color +'      => "\xf2\x13", //non fonctionnel
        'Color -'      => "\xf3\x13", //non fonctionnel
        'Brightness +' => "\xf4\x13", //OK
        'Brightness -' => "\xf5\x13", //OK
        'Contrast +'   => "\xf6\x13", //OK
        'Contrast -'   => "\xf7\x13", //OK
        'Sharpness +'  => "\xf8\x13", //OK
        'Sharpness -'  => "\xf9\x13", //OK
        'Zoom +'       => "\x39\x14", //OK
        'Zoom -'       => "\x3a\x14", //OK
    );

    /*     * ***********************Methodes statiques*************************** */

    /**
     * Renvoie la commande crestron associée
     * @param  string $_name LogicalId de la commande
     * @return string $_name 4-bit Commande brute à envoyer
     */
    public static function getCrestronWriteCommand($_name)
    {
        $_name = (array_key_exists($_name, self::$_writeCmd) == true) ? self::$_writeCmd[$_name] : false;
        return $_name;
    }

    /**
     * Renvoie la commande brute complète en hexadécimal à envoyer
     * @param  string $_name LogicalId de la commande
     * @return string        Commande complète
     */
    public static function getAnalogCmd($_name)
    {
        return self::DIGITAL__JOIN . self::getCrestronWriteCommand($_name);
    }
}
