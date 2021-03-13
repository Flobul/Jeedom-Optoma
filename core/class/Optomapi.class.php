<?php

class Optomapi {

	public function setFullNames($_data) {
        $array = array();
		if (is_array($_data)) {
			foreach ($_data as $key => $value) {
				$fullKey = self::getKeyName($key);
                $array[$fullKey] = $value;
                //$array[$fullKey] = self::getStringValue($fullKey, $value); // à changer en même temps que setValue pour retrouver la commande à envoyer
            }
        }
        return $array;
	}

	public static function getKeyName($_word) {
        static $source = array(
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
        (array_key_exists($_word, $source) == true) ? $_word = $source[$_word] : $_word;
		return $_word;
	}
  
	public static function getStringValue($_key, $_value) {
      
        $option = array(
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
        'Power mode' => array(
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
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        if (array_key_exists($_value, $option[$_key]) == true) {
            $_value = $option[$_key][$_value];
        }
		return $_value;
    }
}
?>