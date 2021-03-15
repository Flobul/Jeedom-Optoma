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

class Optomapi
{

    /*     * *************************Attributs****************************** */
    /**
     * Traduit les code d'erreurs en message
     * @var string
     */
    private static $error = array(
        "0" => "Standby Mode",
        "1" => "Warming up",
        "2" => "Cooling Down",
        "3" => "Out of Range",
        "4" => "Lamp Fail (LED Fail)",
        "5" => "Thermal Switch Error",
        "6" => "Fan Lock",
        "7" => "Over Temperature",
        "8" => "Lamp Hours Running Out",
        "9" => "Cover Open",
        "10" => "Lamp Ignite Fail",
        "11" => "Format Board Power On Fail",
        "12" => "Color Wheel Unexpected Stop",
        "13" => "Over Temperature",
        "14" => "FAN 1 Lock",
        "15" => "FAN 2 Lock",
        "16" => "FAN 3 Lock",
        "17" => "FAN 4 Lock",
        "18" => "FAN 5 Lock",
        "19" => "LAN fail then restart",
        "20" => "LD lower than 60%",
        "21" => "LD NTC (1) Over Temperature",
        "22" => "LD NTC (2) Over Temperature",
        "23" => "High Ambient Temperature",
        "24" => "System Ready"
      );

    /**
     * Traduit les clés de l'API en id logique
     * @var string
     */
    private static $apiSource = array(
        "pw" => "Powerstatus",
        "a" => "Source",
        "b" => "Display Mode",
        "c" => "Brightness",
        "d" => "Contrast",
        "f" => "Sharpness",
        "t" => "Projection",
        "h" => "Brightness Mode",
        "j" => "Mute",
        "k" => "AV Mute",
        "l" => "Power mode",
        "m" => "Volume Audio",
        "n" => "Freeze",
        "o" => "Logo",
        "p" => "3D-2D",
        "q" => "Color Space",
        "r" => "Zoom Value",
        "w" => "3D Mode",
        "x" => "Background Color",
        "y" => "Wall Color",
        "z" => "Volume Micro",
        "A" => "Phase",
        "B" => "Brilliant Color",
        "C" => "Gamma",
        "D" => "Color Temperature",
        "E" => "3D Format",
        "F" => "Internal Speaker",
        "G" => "12V Trigger",
        "H" => "Sleep Timer",
        "I" => "Audio Input",
        "J" => "H. Keystone",
        "K" => "V. Keystone",
        "L" => "Aspect Ratio",
        "M" => "H.Image Shift",
        "N" => "V.Image Shift",
        "O" => "High Altitude",
        "P" => "Direct Power On",
        "Q" => "Projector ID",
        "R" => "Remote Code",
        "S" => "Screen Type",
        "T" => "3D Sync. Invert",
        "U" => "Power",
        "V" => "Information hide",
        "W" => "Display Mode Lock",
        "X" => "Dynamic Black",
        "Y" => "Keypad Lock",
        "e" => "button_up",
        "g" => "button_down",
        "i" => "button_left",
        "s" => "button_right",
        "Z" => "locking Source",
        "ISF" => "isf"
    );
    /**
     * Donne les minValue et maxValue des id logique
     * @var [type]
     */
    private static $rangeOptions = array(
        'Brightness' => array(
            "range" => array(
                -50,
                50
            )
        ) ,
        'Contrast' => array(
            "range" => array(
                -50,
                50
            )
        ) ,
        'Sharpness' => array(
            "range" => array(
                -50,
                50
            )
        ) ,
        'Phase' => array(
            "range" => array(
                0,
                31
            )
        ) ,
        'Brilliant Color' => array(
            "range" => array(
                0,
                10
            )
        ) ,
        'Phase' => array(
            "range" => array(
                0,
                31
            )
        ) ,
        'H. Keystone' => array(
            "range" => array(
                -30,
                30
            )
        ) ,
        'V. Keystone' => array(
            "range" => array(
                -30,
                30
            )
        ) ,
        'H.Image Shift' => array(
            "range" => array(
                -100,
                100
            )
        ) ,
        'V.Image Shift' => array(
            "range" => array(
                -100,
                100
            )
        ) ,
        'Sleep Timer' => array(
            "range" => array(
                0,
                10
            )
        ) ,
        'Projector ID' => array(
            "range" => array(
                0,
                99
            )
        ) ,
        'Remote Code' => array(
            "range" => array(
                0,
                99
            )
        ) ,
        'Volume Audio' => array(
            "range" => array(
                0,
                10
            )
        ) ,
        'Volume Micro' => array(
            "range" => array(
                0,
                10
            )
        )
    );
    /**
     * Donne les listeValue des id logique
     * @var string
     */
    private static $listOptions = array(
        'Source' => array(
            "id" => "source",
            "0" => "HDMI1",
            "1" => "HDMI2",
            "2" => "Component",
            "3" => "VGA",
            "4" => "Media"
        ),
        'Display Mode' => array(
            "id" => "dismode0",
            "0" => "Cinema",
            "1" => "HDR",
            "2" => "HDR SIM",
            "3" => "Game",
            "4" => "Reference",
            "5" => "Bright",
            "6" => "User",
            "7" => "ISF Day",
            "8" => "ISF Night",
            "9" => "3D",
            "10" => "User(3D User)"
        ),
        'Projection' => array(
            "id" => "projection",
            "0" => "Front-Desktop",
            "1" => "Front-Ceiling  ",
            "2" => "Rear-Desktop",
            "3" => "Rear-Ceiling"
        ),
        'Brightness Mode' => array(
            "id" => "lampmd",
            "0" => "Bright",
            "1" => "Eco",
            "2" => "Dynamic"
        ),
        'Power Mode' => array(
            "id" => "pwmode",
            "0" => "Eco",
            "1" => "Active"
        ),
        'Logo' => array(
            "id" => "logo",
            "0" => "Default",
            "2" => "Neutral",
            "1" => "User"
        ),
        '3D-2D' => array(
            "id" => "3dto2d",
            "0" => "3D",
            "1" => "L",
            "2" => "R"
        ),
        'Color Space' => array(
            "id" => "colorsp0",
            "0" => "Auto",
            "1" => "RGB(0~255)",
            "2" => "RGB(16~235)",
            "3" => "YUV(0~255)",
            "4" => "YUV(16~235)"
        ),
        '3D Mode' => array(
            "id" => "3dmode",
            "1" => "DLP-Link",
            "2" => "VESA 3D",
            "3" => "NVIDIA 3D Vision",
            "4" => "IR",
            "0" => "Off"
        ),
        'Background Color' => array(
            "id" => "background",
            "0" => "Reserved",
            "1" => "Blue",
            "2" => "Blaack",
            "3" => "Red",
            "4" => "Green",
            "5" => "White"
        ),
        'Wall Color' => array(
            "id" => "wall",
            "0" => "Off",
            "1" => "Black board",
            "2" => "Light Yellow",
            "3" => "Light Green",
            "4" => "Light Blue",
            "5" => "Pink",
            "6" => "Gray"
        ),
        'Gamma' => array(
            "id" => "Degamma",
            "0" => "Film",
            "1" => "Video",
            "2" => "Graphics",
            "3" => "Standard(2.2)",
            "4" => "1.8",
            "5" => "2.0",
            "6" => "2.4",
            "7" => "3D"
        ),
        'Color Temperature' => array(
            "id" => "colortmp",
            "0" => "D55",
            "1" => "D65",
            "2" => "D75",
            "3" => "D83",
            "4" => "D93",
            "5" => "Native"
        ),
        '3D Format' => array(
            "id" => "3dformat",
            "0" => "Auto",
            "1" => "SBS",
            "2" => "Top and Bottom",
            "4" => "Frame Packing"
        ),
        '12V Trigger' => array(
            "id" => "trigger",
            "1" => "On",
            "0" => "Off"
        ),
        'Audio Input' => array(
            "id" => "audio",
            "0" => "Default",
            "1" => "Audio Input 1",
            "2" => "Audio Input 2",
            "3" => "Audio Input 3"
        ),
        'Aspect Ratio' => array(
            "id" => "aspect0",
            "0" => "4:3",
            "1" => "16:9",
            "2" => "Native",
            "3" => "Auto"
        ),
        'Screen Type' => array(
            "id" => "screen",
            "0" => "16:10",
            "1" => "16:9"
        ),
        'Power' => array(
            "id" => "Power",
            "0" => "100",
            "1" => "95",
            "2" => "90",
            "3" => "85",
            "4" => "80",
            "255" => "Not Support"
            )
        );

    /*     * ***********************Methode static*************************** */
    /**
     * Traduit la clé et la valeur du tableau de l'API
     * @param array $_data Tableau brut de l'API
     * @return array $array Tableau des valeurs traduites
     */
    public static function setFullNames($_data)
    {
        $array = array();
        if (is_array($_data)) {
            foreach ($_data as $key => $value) {
                $fullKey = self::getKeyName($key);
                $array[$fullKey] = $value;
                $array[$fullKey] = self::getValueFromId($fullKey, $value);
            }
        }
        return $array;
    }
    /**
     * Traduit la clé de l'API
     * @param  string $_word Mot non traduit
     * @return string $_word Mot traduit
     */
    public static function getKeyName($_word)
    {
        (array_key_exists($_word, self::$apiSource) == true) ? $_word = $apiSource[$_word] : $_word;
        return $_word;
    }
    /**
     * Traduit la valeur de l'api
     * @param  string $_key Clé non traduite
     * @return string $list Clé traduite
     */
    public static function getListValue($_key)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        $list = '';
        if (is_array(self::$listOptions[$_key])) {
            foreach (self::$listOptions[$_key] as $cle => $valeur) {
                if ($cle !== 'id') {
                    $list .= $valeur . "|" . $valeur . ";";
                }
            }
        }
        return $list;
    }
    /**
     * Récupère les minValue et maxValue de l'id logique
     * @param  string $_key id logique
     * @return array       Tableau des 2 valeurs minValue et maxValue
     */
    public static function getRangeValue($_key)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        $range = array();
        if (array_key_exists('range', self::$rangeOptions[$_key]) == true) {
            $range = self::$rangeOptions[$_key]['range'];
        }
        return $range;
    }
    /**
     * Retourne la valeur de listValue traduite
     * @param  string $_key id logique
     * @param  string $_id  Valeur non traduite
     * @return string $_id  Valeur traduite
     */
    public static function getValueFromId($_key, $_id)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        if (array_key_exists($_id, self::$listOptions[$_key]) == true) {
            $_id = self::$listOptions[$_key][$_id];
        }
        return $_id;
    }
    /**
     * Retourne le nom de l'erreur à partir du code d'erreur
     * @param  string $_key code d'erreur
     * @return string $_id  Message d'erreur traduit
     */
    public static function getError($_key = '')
    {
        if (isset(self::$error[$_key])) {
            $_id = self::$error[$_key];
        } else {
            $_id = "N/A";
        }
        return $_id;
    }
}
