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

class OptomaRs232
{
    /*     * *************************Attributs****************************** */
    const PREFIX = '~';
    const PROJECTOR_ID = '00';
    const WAIT_DELAY = 10;

    /**
     * Traduit les codes d'erreurs en message
     * @var string
     */
    private static $_error = array(
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
     * Donne les minValue et maxValue des id logique
     * @var string
     */
    private static $_rangeOptions = array(
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
                1,
                15
            )
        ) ,
        'Color' => array(
            "range" => array(
                -50,
                50
            )
        ) ,
        'Tint' => array(
            "range" => array(
                -50,
                50
            )
        ) ,
        'Freqency' => array(
            "range" => array(
                -50,
                50
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
        ) ,
        'Treble' => array(
            "range" => array(
                -10,
                10
            )
        ) ,
        'Bass' => array(
            "range" => array(
                -10,
                10
            )
        )
    );

    /**
     * Donne les listValue des id logique
     * @var string
     */
    private static $_readListOptions = array(
        'Powerstatus' => array(
            "0" => "off",
            "1" => "on",
        ),
        'Source' => array(
            "0" => "no signal",
            "1" => "DVI-D DVI-A",
            "2" => "VGA VGA1",
            "3" => "VGA2",
            "4" => "S-Video",
            "5" => "Video",
            "6" => "BNC",
            "7" => "HDMI HDMI1 HDMI/MHL HDMI1/MHL",
            "8" => "HDMI2 HDMI2/MHL",
            "9" => "HDMI3",
            "10" => "Wireless",
            "11" => "Component",
            "12" => "Flash Drive",
            "13" => "Network Display",
            "14" => "USB Display",
            "15" => "DisplayPort",
            "16" => "HDBaseT",
            "17" => "Multimedia",
            "18" => "3G-SDI",
            "20" => "Smart TV"
        ),
        'Display Mode' => array(
            "0" => "None",
            "1" => "Presentation",
            "2" => "Bright",
            "3" => "Cinema",
            "4" => "sRGB - Reference",
            "5" => "User",
            "6" => "User (3D)",
            "7" => "Blackboard",
            "8" => "Classroom",
            "9" => "3D",
            "10" => "DICOM. SIM",
            "11" => "Film",
            "12" => "Game",
            "13" => "Cinema",
            "14" => "Vivid",
            "15" => "ISF Day",
            "16" => "ISF Night",
            "17" => "ISF 3D",
            "18" => "2D High Speed",
            "19" => "Blending",
            "20" => "Sport",
            "21" => "HDR",
            "22" => "HDR SIM"
        ),
        'Projection' => array(
            "0" => "Front",
            "1" => "Rear",
            "2" => "Ceiling-top",
            "3" => "Rear-Top"
        ),
        'Power Mode' => array(
            "0" => "Eco",
            "1" => "Active"
        ),
        'Color Temperature' => array(
            "0" => "Standard",
            "1" => "Cool - D55",
            "2" => "Cold - D65",
            "3" => "Warm",
            "4" => "D75",
            "5" => "D93",
            "6" => "Native (Bright)",
            "7" => "D93"
        ),
        'Aspect Ratio' => array(
            "0" => "None",
            "1" => "4:3",
            "2" => "16:9",
            "3" => "16:10",
            "5" => "LBX",
            "6" => "Native",
            "7" => "Auto",
            "8" => "Auto235",
            "9" => "Superwide",
            "11" => "Auto235_Subtitle",
            "12" => "Auto 3D"
        ),
        'Model Name' => array(
            "1" => "Optoma SVGA",
            "2" => "Optoma XGA",
            "3" => "Optoma WXGA",
            "4" => "Optoma 1080P",
            "5" => "Optoma WUXGA",
            "6" => "Optoma UHD"
        ),
        'Standby Power Mode' => array(
            "1" => "Active",
            "2" => "Eco"
        ),
        'Current Lamp Source' => array(
            "1" => "Lamp1",
            "2" => "Lamp2",
            "3" => "Both"
        ),
    );

    /**
     * Donne les listValue des id logique
     * @var string
     */
    private static $_writeListOptions = array(
        'Powerstatus' => array(
            "0" => "btn_powoff",
            "1" => "btn_powon"
        ),
        'Source' => array(
            "1" => "HDMI HDMI1 HDMI/MHL HDMI1/MHL",
            "2" => "DVI-D",
            "3" => "DVI-A",
            "4" => "BNC",
            "5" => "VGA VGA1",
            "6" => "VGA2",
            "9" => "S-Video",
            "10" => "Video",
            "11" => "Wireless",
            "14" => "Component",
            "15" => "HDMI2 HDMI2/MHL",
            "16" => "HDMI3",
            "17" => "Flash Drive",
            "18" => "Network Display",
            "19" => "USB Display",
            "20" => "DisplayPort",
            "21" => "HDBaseT",
            "22" => "3G-SDI",
            "23" => "Multimedia",
            "24" => "Smart TV"
        ),
        'Display Mode' => array(
            "1" => "Presentation",
            "2" => "Bright",
            "3" => "Cinema",
            "4" => "sRGB - Reference",
            "5" => "User",
            "6" => "User (3D)",
            "9" => "3D",
            "11" => "Film",
            "12" => "Game",
            "13" => "DICOM. SIM",
            "14" => "ISF Day",
            "15" => "ISF Night",
            "16" => "Vivid",
            "17" => "ISF 3D",
            "18" => "2D High Speed",
            "19" => "Blending",
            "20" => "Sport",
            "21" => "HDR",
            "22" => "HDR SIM"
        ),
        'Projection' => array(
            "1" => "Front",
            "2" => "Rear",
            "3" => "Ceiling-top",
            "4" => "Rear-Top"
        ),
        'Brightness Mode' => array(
            "1" => "Bright",
            "2" => "Eco",
            "3" => "Eco+",
            "4" => "Dynamic",
            "5" => "Power",
            "6" => "Constant Power",
            "7" => "Constant Luminace",
        ),
        'Dynamic Black' => array(
            "0" => "Off",
            "1" => "1",
            "2" => "2",
            "3" => "3"
        ),
        'Power Mode' => array(
            "0" => "Eco",
            "1" => "Active"
        ),
        'Logo' => array(
            "1" => "Default",
            "2" => "User",
            "3" => "Neutral"
        ),
        '3D-2D' => array(
            "0" => "3D",
            "1" => "L",
            "2" => "R"
        ),
        'Color Space' => array(
            "1" => "Auto",
            "2" => "RGB(0~255)",
            "3" => "YUV",
            "3" => "YUV(0~255)",
            "4" => "RGB(16~235)",
            "5" => "Rec. 709",
            "6" => "Rec. 601"
        ),
        '3D Mode' => array(
            "0" => "Off",
            "1" => "DLP-Link",
            "3" => "IR VESA"
        ),
        'Background Color' => array(
            "0" => "None",
            "1" => "Blue",
            "3" => "Red",
            "4" => "Green",
            "5" => "White",
            "6" => "Gray",
            "7" => "Logo"
        ),
        'Wall Color' => array(
            "0" => "Off",
            "1" => "BlackBoard",
            "7" => "Light Yellow",
            "3" => "Light Green",
            "4" => "Light Blue",
            "5" => "Pink",
            "6" => "Gray"
        ),
        'Gamma' => array(
            "1" => "Film",
            "2" => "Video",
            "3" => "Graphics",
            "4" => "Standard(2.2)",
            "5" => "1.8",
            "6" => "2.0",
            "8" => "2.6",
            "9" => "3D",
            "10" => "BlackBoard",
            "11" => "DICOM. SIM",
            "12" => "2.4 - Bright",
            "13" => "CRT",
            "14" => "User",
        ),
        'Color Temperature' => array(
            "1" => "Standard - D65",
            "2" => "Cool - D75",
            "3" => "Cold - D83",
            "4" => "Warm - D55",
            "5" => "Native (Bright)",
            "6" => "D93"
        ),
        '3D Format' => array(
            "0" => "Auto",
            "1" => "SBS",
            "2" => "Top and Bottom",
            "3" => "Frame Sequential",
            "7" => "Frame Packing",
            "8" => "Off"
        ),
        '12V Trigger' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Audio Input' => array(
            "0" => "Default",
            "1" => "Audio Input 1",
            "2" => "RCA",
            "3" => "Audio Input 2",
            "4" => "Audio Input 3",
            "5" => "Audio Input 4",
            "6" => "HDMI 1",
            "7" => "HDMI 2",
            "8" => "Audio 5 Displayport",
            "9" => "Displayport"
        ),
        'Aspect Ratio' => array(
            "1" => "4:3",
            "2" => "16:9",
            "3" => "16:10",
            "5" => "LBX",
            "6" => "Native",
            "7" => "Auto",
            "8" => "Auto235",
            "9" => "Superwide",
            "11" => "Auto235_Subtitle",
            "12" => "Auto 3D"
        ),
        'Screen Type' => array(
            "0" => "16:9",
            "1" => "16:10"
        ),
        'PureMotion' => array(
            "0" => "Off",
            "1" => "1",
            "2" => "2",
            "3" => "3"
        ),
        '2D-3D' => array(
            "0" => "Off",
            "1" => "1",
            "2" => "2",
            "3" => "3"
        ),
        '3D Sync. Invert' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Direct Power On' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'High Altitude' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Display Mode Lock' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Keypad Lock' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Information Hide' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Beep' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Freeze' => array(
            "0" => "Unfreeze",
            "1" => "Freeze"
        ),
        'Mute' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'AV Mute' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Internal Speaker' => array(
            "0" => "Off",
            "1" => "On"
        ),
        'Brightness Power' => array(
            "0" => "100%",
            "1" => "95%",
            "2" => "90%",
            "3" => "85%",
            "4" => "80%",
            "5" => "75%",
            "6" => "70%",
            "7" => "65%",
            "8" => "60%",
            "9" => "55%",
            "10" => "50%"
        )
    );

    /**
     * Donne la commande read associée au protocole
     * @var string
     */
    private static $_readCmd = array(
        'LAN Network Status' => array(
            "telnet" => "87 1"
        ),
        'LAN IP Address' => array(
            "telnet" => "87 3"
        ),
        'Lamp Hours Bright' => array(
            "telnet" => "108 3"
        ),
        'Lamp Hours Eco' => array(
            "telnet" => "108 4"
        ),
        'Lamp Hours Dynamic' => array(
            "telnet" => "108 5"
        ),
        'Lamp Hours Eco+' => array(
            "telnet" => "108 6"
        ),
        'Lamp Hours Total' => array(
            "telnet" => "108 1"
        ),
        'Lamp Hours Lamp 2 Hour' => array(
            "telnet" => "108 2"
        ),
        'Source' => array(
            "telnet" => "121 1"
        ),
        'RS232 Version' => array(
            "telnet" => "122 1"
        ),
        'Display Mode' => array(
            "telnet" => "123 1"
        ),
        'Power' => array(
            "telnet" => "124 1"
        ),
        'Powerstatus' => array(
            "telnet" => "124 1"
        ),
        'Brightness' => array(
            "telnet" => "125 1"
        ),
        'Contrast' => array(
            "telnet" => "126 1"
        ),
        'Aspect Ratio' => array(
            "telnet" => "127 1"
        ),
        'Color Temperature' => array(
            "telnet" => "128 1"
        ),
        'Projection' => array(
            "telnet" => "129 1"
        ),
        'Power Mode' => array(
            "telnet" => "150 16"
        ),
        'Output 3D state' => array(
            "telnet" => "130 1"
        ),
        'Info String' => array(
            "telnet" => "150 1"
        ),
        'Native Resolution' => array(
            "telnet" => "150 2"
        ),
        'Main Source' => array(
            "telnet" => "150 3"
        ),
        'Main Source Resolution' => array(
            "telnet" => "150 4"
        ),
        'Resolution' => array(
            "telnet" => "150 4"
        ),
        'Main Source Signal Format' => array(
            "telnet" => "150 5"
        ),
        'Main Source Pixel Clock' => array(
            "telnet" => "150 6"
        ),
        'Main Source Horz Refresh' => array(
            "telnet" => "150 7"
        ),
        'Main Source Vert Refresh' => array(
            "telnet" => "150 8"
        ),
        'Sub Source' => array(
            "telnet" => "150 9"
        ),
        'Sub Source Resolution' => array(
            "telnet" => "150 10"
        ),
        'Sub Source Signal Format' => array(
            "telnet" => "150 11"
        ),
        'Sub Source Pixel Clock' => array(
            "telnet" => "150 12"
        ),
        'Sub Source Horz Refresh' => array(
            "telnet" => "150 13"
        ),
        'Sub Source Vert Refresh' => array(
            "telnet" => "150 14"
        ),
        'Light Source Mode' => array(
            "telnet" => "150 15"
        ),
        'Standby Power Mode' => array(
            "telnet" => "150 16"
        ),
        'LAN DHCP' => array(
            "telnet" => "150 17"
        ),
        'System Temperature' => array(
            "telnet" => "150 18"
        ),
        'Refresh rate' => array(
            "telnet" => "150 19"
        ),
        'Current Lamp Source' => array(
            "telnet" => "150 20"
        ),
        'Model Name' => array(
            "telnet" => "151 1"
        ),
        'Filter Usage Hours' => array(
            "telnet" => "321 1"
        ),
        'System Temperature' => array(
            "telnet" => "352 1"
        ),
        'Serial Number' => array(
            "telnet" => "353 1"
        ),
        'AV Mute' => array(
            "telnet" => "355 1"
        ),
        'Mute' => array(
            "telnet" => "356 1"
        ),
        'LAN FW Version' => array(
            "telnet" => "357 1"
        ),
        'Fan 1 Speed' => array(
            "telnet" => "357 1"
        ),
        'Fan 2 Speed' => array(
            "telnet" => "357 2"
        ),
        'Fan 3 Speed' => array(
            "telnet" => "357 3"
        ),
        'Fan 4 Speed' => array(
            "telnet" => "357 4"
        ),
        'Current Watt' => array(
            "telnet" => "358 1"
        ),
        'WLAN Network Status' => array(
            "telnet" => "451 1"
        ),
        'WLAN IP Address' => array(
            "telnet" => "451 2"
        ),
        'WLAN SSID' => array(
            "telnet" => "451 3"
        ),
        'LAN MAC Address' => array(
            "telnet" => "555 1"
        ),
        'WLAN MAC Address' => array(
            "telnet" => "555 2"
        ),
        'Projector ID' => array(
            "telnet" => "558 1"
        ),
    );

    /**
     * Donne la commande send associée au protocole
     * @var string
     */
    private static $_sendCmd = array(
        'Powerstatus' => array(
            'other' => "00"
        ),
        'Resync' => array(
            'other' => "01 1"
        ),
        'AV Mute' => array(
            'other' => "02"
        ),
        'Mute' => array(
            'other' => "03"
        ),
        'Audio Mute' => array(
            'other' => "80"
        ),
        'Audio Mic' => array(
            'other' => "562"
        ),
        'Volume Audio' => array(
            '-' => "140 17",
            '+' => "140 18",
            'slider' => "81"
        ),
        'Volume Micro' => array(
            '-' => "140 17",
            '+' => "140 18",
            'slider' => "93"
        ),
        'Audio Input' => array(
            'select' => "89"
        ),
        'Projection' => array(
            'select' => "71"
        ),
        'Screen Type' => array(
            'select' => "90"
        ),
        'Direct Power On' => array(
            'select' => "105"
        ),
        'Sleep Timer' => array(
            'slider' => "107"
        ),
        'Power Mode' => array(
            'slider' => "114"
        ),
        'Remote Code' => array(
            '-' => "48 1",
            '+' => "48 2",
            'slider' => "350"
        ),
        'Freeze' => array( // OK
            'other' => "04"
        ),
        'Internal Speaker' => array( // OK
            'other' => "310"
        ),
        'Source' => array(
            'select' => "12"
        ),
        'Sub Source' => array(
            'select' => "305"
        ),
        'Display Mode' => array(
            'select' => "20"
        ),
        'Wall Color' => array(
            'other' => "506"
        ),
        'Brightness' => array(
            '-' => "46 1",
            '+' => "46 2",
            'slider' => "21"
        ),
        'Contrast' => array(
            '-' => "47 1",
            '+' => "47 2",
            'slider' => "22"
        ),
        'Sharpness' => array(
            'slider' => "23"
        ),
        'Color' => array(
            'slider' => "45"
        ),
        'Tint' => array(
            'slider' => "44"
        ),
        'Brilliant Color' => array(
            'slider' => "34"
        ),
        'Brightness Mode' => array(
            'select' => "110"
        ),
        'Brightness Power' => array(
            'select' => "326"
        ),
        'PureMotion' => array(
            'select' => "190"
        ),
        '3D Mode' => array(
            'select' => "230"
        ),
        '3D-2D' => array(
            'select' => "400"
        ),
        '3D Format' => array(
            'select' => "405"
        ),
        '3D Sync. Invert' => array(
            'select' => "231"
        ),
        '2D-3D' => array(
            'select' => "402"
        ),
        'Aspect Ratio' => array(
            'select' => "60"
        ),
        'H.Image Shift' => array(
            'slider' => "63"
        ),
        'V.Image Shift' => array(
            'slider' => "64"
        ),
        'H. Keystone'=> array(
            'slider' => "65"
        ),
        'V. Keystone'=> array(
            'slider' => "66"
        ),
        'Gamma' => array( // OK
            'select' => "35"
        ),
        'Color Temperature' => array( // OK
            'select' => "36"
        ),
        'Color Space' => array(
            'select' => "37"
        ),
        'Projector ID' => array(
            'slider' => "79"
        ),
        '12V Trigger' => array(
            'other' => "192"
        ),
        'High Altitude' => array(
            'other' => "101"
        ),
        'Display Mode Lock' => array(
            'other' => "348"
        ),
        'Keypad Lock' => array(
            'other' => "103"
        ),
        'Information Hide' => array(
            'other' => "102"
        ),
        'Logo' => array(
            'select' => "82"
        ),
        'Logo Capture' => array(
            'other' => "83 1"
        ),
        'Background Color' => array(
            'select' => "104"
        ),
        'Beep' => array(
            'other' => "503"
        ),
        'Dynamic Black' => array(
            'select' => "191"
        ),
        'Phase' => array(
            'slider' => "74"
        ),
        'Remote Control' => array(
            'Power' => "140 1",
            'Power Off' => "140 2",
            'Remote Mouse Up' => "140 3",
            'Remote Mouse Left' => "140 4",
            'Remote Mouse Enter' => "140 5",
            'Remote Mouse Right' => "140 6",
            'Remote Mouse Down' => "140 7",
            'Mouse Left Click' => "140 8",
            'Mouse Right Click' => "140 9",
            'Up' => "140 10",
            'Left' => "140 11",
            'Enter' => "140 12",
            'Right' => "140 13",
            'Down' => "140 14",
            'V Keystone +' => "140 15",
            'V Keystone -' => "140 16",
            'Volume -' => "140 17",
            'Volume +' => "140 18",
            'Brightness' => "140 19",
            'Menu' => "140 20",
            'Zoom' => "140 21",
            'DVI-D' => "140 22",
            'VGA-1' => "140 23",
            'AV Mute' => "140 24",
            'S-Video' => "140 25",
            'VGA-2' => "140 26",
            'Video' => "140 27",
            'Contrast' => "140 28",
            'Freeze' => "140 30",
            'Lens shift' => "140 31",
            'Zoom +' => "140 32",
            'Zoom -' => "140 33",
            'Focus +' => "140 34",
            'Focus -' => "140 35",
            'Mode' => "140 36",
            'Aspect Ratio' => "140 37",
            '12 trigger On' => "140 38",
            '12 trigger Off' => "140 39",
            'Info' => "140 40",
            'Resync' => "140 41",
            'HDMI 1' => "140 42",
            'HDMI 2' => "140 43",
            'BNC' => "140 44",
            'Component' => "140 45",
            'Source' => "140 47",
            '1' => "140 51",
            '2' => "140 52",
            '3' => "140 53",
            '4' => "140 54",
            '5' => "140 55",
            '6' => "140 56",
            '7' => "140 57",
            '8' => "140 58",
            '9' => "140 59",
            '0' => "140 60",
            'Gamma' => "140 61",
            'PIP' => "140 63",
            'Lens H left' => "140 64",
            'Lens H right' => "140 65",
            'Lens V left' => "140 66",
            'Lens V right' => "140 67",
            'H Keystone +' => "140 68",
            'H Keystone -' => "140 69",
            'Hot Key F1' => "140 70",
            'Hot Key F2' => "140 71",
            'Hot Key F3' => "140 72",
            'Pattern' => "140 73",
            'Exit' => "140 74",
            'HDMI 3' => "140 75",
            'Display Mode' => "140 76",
            'Mute' => "140 77",
            '3D' => "140 78",
            'DB' => "140 79",
            'Sleep Timer' => "140 80",
            'Home' => "140 81",
            'Return' => "140 82"
        )
    );

    /*     * ***********************Methodes statiques*************************** */

    /** Write Command
     *  ~         | X X          | X X X   |       | n        | CR
     *  Lead Code | Projector ID | Command | space | variable | carriage return
     *  Prefix    | 00~99        | 000~999 |       | 0~9999   | suffix
     *  Response Format
     *  Pass: P
     *  Fail: F
     *
     *  Read Command
     *  ~         | X X          | X X X   |       | n        | CR
     *  Lead Code | Projector ID | Command | space | variable | carriage return
     *  Prefix    | 00~99        | 000~999 |       | 0~9999   | suffix
     *  Response Format
     *  Pass:     | O k          | n                    Fail: | F
     *            |              | Variable
     *
     * System Automatically Send
     *            | I N          | F O     | n
     *                                     | Variable
     */

    /**
     * Renvoie un tableau des informations complètes pour renseigner les commandes
     * @param  string $_data String reçu de la commande telnet sans "Ok" (012345010C0401)
     * @return array $info        Tableau des valeurs traduites
     */
    public static function getInformations($_data)
    {
        $info = array();
        $info['Powerstatus'] = self::getReadListValue('Powerstatus', substr($_data, 0, 1));
        (is_numeric(substr($_data, 1, 5))) ? $info['Lamp Hours Total'] = substr($_data, 1, 5) : "";
        $source = (intval(substr($_data, 6, 2)) < 10) ? substr($_data, 7, 1) : substr($_data, 6, 2);
        $info['Source'] = self::getReadListValue('Source', $source);
        $info['Firmware Version'] = substr($_data, 8, 4);
        $displayMode = (intval(substr($_data, 12, 2)) < 10) ? substr($_data, 13, 1) : substr($_data, 12, 2);
        $info['Display Mode'] = self::getReadListValue('Display Mode', $displayMode);
        return $info;
    }

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
     * Renvoie la commande telnet associée
     * @param  string $_word Mot non traduit
     * @return string $_word Mot traduit
     */
    public static function getRS232Command($_name)
    {
        $_name = (array_key_exists($_name, self::$_readCmd) == true) ? self::$_readCmd[$_name]['telnet'] : false;
        return $_name;
    }

    /**
     * Traduit la clé de l'API
     * @param  string $_word Mot non traduit
     * @return string $_word Mot traduit
     */
    public static function getKeyName($_word)
    {
        (array_key_exists($_word, self::$_apiSource) == true) ? $_word = self::$_apiSource[$_word] : $_word;
        return $_word;
    }

    /**
     * Traduit la valeur de l'api
     * @param  string $_key Clé non traduite
     * @return string $list Clé traduite
     */
    public static function getReadListValue($_key, $_value)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        if (array_key_exists($_value, self::$_readListOptions[$_key]) == true) {
            $_value = self::$_readListOptions[$_key][$_value];
        }
        return $_value;
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
        if (is_array(self::$_readListOptions[$_key])) {
            foreach (self::$_readListOptions[$_key] as $cle => $valeur) {
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
        if (array_key_exists('range', self::$_rangeOptions[$_key]) == true) {
            $range = self::$_rangeOptions[$_key]['range'];
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
        if (array_key_exists($_id, self::$_listOptions[$_key]) == true) {
            $_id = self::$_listOptions[$_key][$_id];
        }
        return $_id;
    }

    public static function getIdFromValue($_key, $_id)
    {
        $_key = str_replace(array('&', '#', ']', '[', '%', "'", "/"), '', $_key);
        if (is_array(self::$_writeListOptions[$_key]) == true) {
            foreach (self::$_writeListOptions[$_key] as $cle => $valeur) {
                if ($valeur === $_id) {
                    $_id = $cle;
                }
            }
        }
        return $_id;
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

    /**
     * Retourne le nom de l'erreur à partir du code d'erreur
     * @param  string $_key code d'erreur
     * @return string $_id  Message d'erreur traduit
     */
    public static function getError($_key = '')
    {
        if (isset(self::$_error[$_key])) {
            $_id = self::$_error[$_key];
        } else {
            $_id = "N/A";
        }
        return $_id;
    }
}
