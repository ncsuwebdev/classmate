<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file _LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    RSPM
 * @subpackage Timezone
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 * @license    BSD License
 * @author     Jason Austin
 * @author     Garrison Locke
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: $
 */

/**
 * Model to show all timezones
 *
 * @package    RSPM
 * @subpackage Timezone
 * @category   Model
 * @copyright  Copyright (c) 2007 NC State University Office of Information Technology
 *
 */
final class Timezone {
        
    protected static $_timezoneList = array(
    
                                        'Africa/Abidjan' => 'Africa/Abidjan',
                                        'Africa/Accra' => 'Africa/Accra',
                                        'Africa/Addis_Ababa' => 'Africa/Addis_Ababa',
                                        'Africa/Algiers' => 'Africa/Algiers',
                                        'Africa/Asmera' => 'Africa/Asmera',
                                        'Africa/Bamako' => 'Africa/Bamako',
                                        'Africa/Bangui' => 'Africa/Bangui',
                                        'Africa/Banjul' => 'Africa/Banjul',
                                        'Africa/Bissau' => 'Africa/Bissau',
                                        'Africa/Blantyre' => 'Africa/Blantyre',
                                        'Africa/Brazzaville' => 'Africa/Brazzaville',
                                        'Africa/Bujumbura' => 'Africa/Bujumbura',
                                        'Africa/Cairo' => 'Africa/Cairo',
                                        'Africa/Casablanca' => 'Africa/Casablanca',
                                        'Africa/Ceuta' => 'Africa/Ceuta',
                                        'Africa/Conakry' => 'Africa/Conakry',
                                        'Africa/Dakar' => 'Africa/Dakar',
                                        'Africa/Dar_es_Salaam' => 'Africa/Dar_es_Salaam',
                                        'Africa/Djibouti' => 'Africa/Djibouti',
                                        'Africa/Douala' => 'Africa/Douala',
                                        'Africa/El_Aaiun' => 'Africa/El_Aaiun',
                                        'Africa/Freetown' => 'Africa/Freetown',
                                        'Africa/Gaborone' => 'Africa/Gaborone',
                                        'Africa/Hara' => 'Africa/Hara',
                                        'Africa/Johannesburg' => 'Africa/Johannesburg',
                                        'Africa/Kampala' => 'Africa/Kampala',
                                        'Africa/Khartoum' => 'Africa/Khartoum',
                                        'Africa/Kigali' => 'Africa/Kigali',
                                        'Africa/Kinshasa' => 'Africa/Kinshasa',
                                        'Africa/Lagos' => 'Africa/Lagos',
                                        'Africa/Libreville' => 'Africa/Libreville',
                                        'Africa/Lome' => 'Africa/Lome',
                                        'Africa/Luanda' => 'Africa/Luanda',
                                        'Africa/Lubumbashi' => 'Africa/Lubumbashi',
                                        'Africa/Lusaka' => 'Africa/Lusaka',
                                        'Africa/Malabo' => 'Africa/Malabo',
                                        'Africa/Maputo' => 'Africa/Maputo',
                                        'Africa/Maseru' => 'Africa/Maseru',
                                        'Africa/Mbabane' => 'Africa/Mbabane',
                                        'Africa/Mogadishu' => 'Africa/Mogadishu',
                                        'Africa/Monrovia' => 'Africa/Monrovia',
                                        'Africa/Nairobi' => 'Africa/Nairobi',
                                        'Africa/Ndjamena' => 'Africa/Ndjamena',
                                        'Africa/Niamey' => 'Africa/Niamey',
                                        'Africa/Nouakchott' => 'Africa/Nouakchott',
                                        'Africa/Ouagadougou' => 'Africa/Ouagadougou',
                                        'Africa/Porto-Novo' => 'Africa/Porto-Novo',
                                        'Africa/Sao_Tome' => 'Africa/Sao_Tome',
                                        'Africa/Timbuktu' => 'Africa/Timbuktu',
                                        'Africa/Tripoli' => 'Africa/Tripoli',
                                        'Africa/Tunis' => 'Africa/Tunis',
                                        'Africa/Windhoek' => 'Africa/Windhoek',
                                        'America/Adak' => 'America/Adak',
                                        'America/Anchorage' => 'America/Anchorage',
                                        'America/Anguilla' => 'America/Anguilla',
                                        'America/Antigua' => 'America/Antigua',
                                        'America/Araguaina' => 'America/Araguaina',
                                        'America/Aruba' => 'America/Aruba',
                                        'America/Asuncion' => 'America/Asuncion',
                                        'America/Barbados' => 'America/Barbados',
                                        'America/Belem' => 'America/Belem',
                                        'America/Belize' => 'America/Belize',
                                        'America/Bogota' => 'America/Bogota',
                                        'America/Boise' => 'America/Boise',
                                        'America/Buenos_Aires' => 'America/Buenos_Aires',
                                        'America/Cancun' => 'America/Cancun',
                                        'America/Caracas' => 'America/Caracas',
                                        'America/Catamarca' => 'America/Catamarca',
                                        'America/Cayenne' => 'America/Cayenne',
                                        'America/Cayman' => 'America/Cayman',
                                        'America/Chicago' => 'America/Chicago',
                                        'America/Chihuahua' => 'America/Chihuahua',
                                        'America/Cordoba' => 'America/Cordoba',
                                        'America/Costa_Rica' => 'America/Costa_Rica',
                                        'America/Cuiaba' => 'America/Cuiaba',
                                        'America/Curacao' => 'America/Curacao',
                                        'America/Dawson' => 'America/Dawson',
                                        'America/Dawson_Creek' => 'America/Dawson_Creek',
                                        'America/Denver' => 'America/Denver',
                                        'America/Detroit' => 'America/Detroit',
                                        'America/Dominica' => 'America/Dominica',
                                        'America/Edmonton' => 'America/Edmonton',
                                        'America/El_Salvador' => 'America/El_Salvador',
                                        'America/Ensenada' => 'America/Ensenada',
                                        'America/Fortaleza' => 'America/Fortaleza',
                                        'America/Glace_Bay' => 'America/Glace_Bay',
                                        'America/Godthab' => 'America/Godthab',
                                        'America/Goose_Bay' => 'America/Goose_Bay',
                                        'America/Grand_Turk' => 'America/Grand_Turk',
                                        'America/Grenada' => 'America/Grenada',
                                        'America/Guadeloupe' => 'America/Guadeloupe',
                                        'America/Guatemala' => 'America/Guatemala',
                                        'America/Guayaquil' => 'America/Guayaquil',
                                        'America/Guyana' => 'America/Guyana',
                                        'America/Halifax' => 'America/Halifax',
                                        'America/Havana' => 'America/Havana',
                                        'America/Indiana/Knox' => 'America/Indiana/Knox',
                                        'America/Indiana/Marengo' => 'America/Indiana/Marengo',
                                        'America/Indiana/Vevay' => 'America/Indiana/Vevay',
                                        'America/Indianapolis' => 'America/Indianapolis',
                                        'America/Inuvik' => 'America/Inuvik',
                                        'America/Iqaluit' => 'America/Iqaluit',
                                        'America/Jamaica' => 'America/Jamaica',
                                        'America/Jujuy' => 'America/Jujuy',
                                        'America/Juneau' => 'America/Juneau',
                                        'America/La_Paz' => 'America/La_Paz',
                                        'America/Lima' => 'America/Lima',
                                        'America/Los_Angeles' => 'America/Los_Angeles',
                                        'America/Louisville' => 'America/Louisville',
                                        'America/Maceio' => 'America/Maceio',
                                        'America/Managua' => 'America/Managua',
                                        'America/Manaus' => 'America/Manaus',
                                        'America/Martinique' => 'America/Martinique',
                                        'America/Mazatlan' => 'America/Mazatlan',
                                        'America/Mendoza' => 'America/Mendoza',
                                        'America/Menominee' => 'America/Menominee',
                                        'America/Mexico_City' => 'America/Mexico_City',
                                        'America/Miquelon' => 'America/Miquelon',
                                        'America/Montevideo' => 'America/Montevideo',
                                        'America/Montreal' => 'America/Montreal',
                                        'America/Montserrat' => 'America/Montserrat',
                                        'America/Nassau' => 'America/Nassau',
                                        'America/New_York' => 'America/New_York',
                                        'America/Nipigon' => 'America/Nipigon',
                                        'America/Nome' => 'America/Nome',
                                        'America/Noronha' => 'America/Noronha',
                                        'America/Panama' => 'America/Panama',
                                        'America/Pangnirtung' => 'America/Pangnirtung',
                                        'America/Paramaribo' => 'America/Paramaribo',
                                        'America/Phoenix' => 'America/Phoenix',
                                        'America/Port-au-Prince' => 'America/Port-au-Prince',
                                        'America/Port_of_Spain' => 'America/Port_of_Spain',
                                        'America/Porto_Acre' => 'America/Porto_Acre',
                                        'America/Porto_Velho' => 'America/Porto_Velho',
                                        'America/Puerto_Rico' => 'America/Puerto_Rico',
                                        'America/Rainy_River' => 'America/Rainy_River',
                                        'America/Rankin_Inlet' => 'America/Rankin_Inlet',
                                        'America/Regina' => 'America/Regina',
                                        'America/Rosario' => 'America/Rosario',
                                        'America/Santiago' => 'America/Santiago',
                                        'America/Santo_Domingo' => 'America/Santo_Domingo',
                                        'America/Sao_Paulo' => 'America/Sao_Paulo',
                                        'America/Scoresbysund' => 'America/Scoresbysund',
                                        'America/Shiprock' => 'America/Shiprock',
                                        'America/St_Johns' => 'America/St_Johns',
                                        'America/St_Kitts' => 'America/St_Kitts',
                                        'America/St_Lucia' => 'America/St_Lucia',
                                        'America/St_Thomas' => 'America/St_Thomas',
                                        'America/St_Vincent' => 'America/St_Vincent',
                                        'America/Swift_Current' => 'America/Swift_Current',
                                        'America/Tegucigalpa' => 'America/Tegucigalpa',
                                        'America/Thule' => 'America/Thule',
                                        'America/Thunder_Bay' => 'America/Thunder_Bay',
                                        'America/Tijuana' => 'America/Tijuana',
                                        'America/Tortola' => 'America/Tortola',
                                        'America/Vancouver' => 'America/Vancouver',
                                        'America/Whitehorse' => 'America/Whitehorse',
                                        'America/Winnipeg' => 'America/Winnipeg',
                                        'America/Yakutat' => 'America/Yakutat',
                                        'America/Yellowknife' => 'America/Yellowknife',
                                        'Antarctica/Casey' => 'Antarctica/Casey',
                                        'Antarctica/Davis' => 'Antarctica/Davis',
                                        'Antarctica/DumontDUrville' => 'Antarctica/DumontDUrville',
                                        'Antarctica/Mawson' => 'Antarctica/Mawson',
                                        'Antarctica/McMurdo' => 'Antarctica/McMurdo',
                                        'Antarctica/Palmer' => 'Antarctica/Palmer',
                                        'Antarctica/South_Pole' => 'Antarctica/South_Pole',
                                        'Arctic/Longyearbyen' => 'Arctic/Longyearbyen',
                                        'Asia/Aden' => 'Asia/Aden',
                                        'Asia/Almaty' => 'Asia/Almaty',
                                        'Asia/Amman' => 'Asia/Amman',
                                        'Asia/Anadyr' => 'Asia/Anadyr',
                                        'Asia/Aqtau' => 'Asia/Aqtau',
                                        'Asia/Aqtobe' => 'Asia/Aqtobe',
                                        'Asia/Ashkhabad' => 'Asia/Ashkhabad',
                                        'Asia/Baghdad' => 'Asia/Baghdad',
                                        'Asia/Bahrain' => 'Asia/Bahrain',
                                        'Asia/Baku' => 'Asia/Baku',
                                        'Asia/Bangkok' => 'Asia/Bangkok',
                                        'Asia/Beirut' => 'Asia/Beirut',
                                        'Asia/Bishkek' => 'Asia/Bishkek',
                                        'Asia/Brunei' => 'Asia/Brunei',
                                        'Asia/Calcutta' => 'Asia/Calcutta',
                                        'Asia/Chungking' => 'Asia/Chungking',
                                        'Asia/Colombo' => 'Asia/Colombo',
                                        'Asia/Dacca' => 'Asia/Dacca',
                                        'Asia/Damascus' => 'Asia/Damascus',
                                        'Asia/Dubai' => 'Asia/Dubai',
                                        'Asia/Dushanbe' => 'Asia/Dushanbe',
                                        'Asia/Gaza' => 'Asia/Gaza',
                                        'Asia/Harbin' => 'Asia/Harbin',
                                        'Asia/Hong_Kong' => 'Asia/Hong_Kong',
                                        'Asia/Irkutsk' => 'Asia/Irkutsk',
                                        'Asia/Jakarta' => 'Asia/Jakarta',
                                        'Asia/Jayapura' => 'Asia/Jayapura',
                                        'Asia/Jerusalem' => 'Asia/Jerusalem',
                                        'Asia/Kabul' => 'Asia/Kabul',
                                        'Asia/Kamchatka' => 'Asia/Kamchatka',
                                        'Asia/Karachi' => 'Asia/Karachi',
                                        'Asia/Kashgar' => 'Asia/Kashgar',
                                        'Asia/Katmandu' => 'Asia/Katmandu',
                                        'Asia/Krasnoyarsk' => 'Asia/Krasnoyarsk',
                                        'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur',
                                        'Asia/Kuching' => 'Asia/Kuching',
                                        'Asia/Kuwait' => 'Asia/Kuwait',
                                        'Asia/Macao' => 'Asia/Macao',
                                        'Asia/Magadan' => 'Asia/Magadan',
                                        'Asia/Manila' => 'Asia/Manila',
                                        'Asia/Muscat' => 'Asia/Muscat',
                                        'Asia/Nicosia' => 'Asia/Nicosia',
                                        'Asia/Novosibirsk' => 'Asia/Novosibirsk',
                                        'Asia/Omsk' => 'Asia/Omsk',
                                        'Asia/Phnom_Penh' => 'Asia/Phnom_Penh',
                                        'Asia/Pyongyang' => 'Asia/Pyongyang',
                                        'Asia/Qatar' => 'Asia/Qatar',
                                        'Asia/Rangoon' => 'Asia/Rangoon',
                                        'Asia/Riyadh' => 'Asia/Riyadh',
                                        'Asia/Saigon' => 'Asia/Saigon',
                                        'Asia/Samarkand' => 'Asia/Samarkand',
                                        'Asia/Seoul' => 'Asia/Seoul',
                                        'Asia/Shanghai' => 'Asia/Shanghai',
                                        'Asia/Singapore' => 'Asia/Singapore',
                                        'Asia/Taipei' => 'Asia/Taipei',
                                        'Asia/Tashkent' => 'Asia/Tashkent',
                                        'Asia/Tbilisi' => 'Asia/Tbilisi',
                                        'Asia/Tehran' => 'Asia/Tehran',
                                        'Asia/Thimbu' => 'Asia/Thimbu',
                                        'Asia/Tokyo' => 'Asia/Tokyo',
                                        'Asia/Ujung_Pandang' => 'Asia/Ujung_Pandang',
                                        'Asia/Ulan_Bator' => 'Asia/Ulan_Bator',
                                        'Asia/Urumqi' => 'Asia/Urumqi',
                                        'Asia/Vientiane' => 'Asia/Vientiane',
                                        'Asia/Vladivostok' => 'Asia/Vladivostok',
                                        'Asia/Yakutsk' => 'Asia/Yakutsk',
                                        'Asia/Yekaterinburg' => 'Asia/Yekaterinburg',
                                        'Asia/Yerevan' => 'Asia/Yerevan',
                                        'Atlantic/Azores' => 'Atlantic/Azores',
                                        'Atlantic/Bermuda' => 'Atlantic/Bermuda',
                                        'Atlantic/Canary' => 'Atlantic/Canary',
                                        'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
                                        'Atlantic/Faeroe' => 'Atlantic/Faeroe',
                                        'Atlantic/Jan_Mayen' => 'Atlantic/Jan_Mayen',
                                        'Atlantic/Madeira' => 'Atlantic/Madeira',
                                        'Atlantic/Reykjavik' => 'Atlantic/Reykjavik',
                                        'Atlantic/South_Georgia' => 'Atlantic/South_Georgia',
                                        'Atlantic/St_Helena' => 'Atlantic/St_Helena',
                                        'Atlantic/Stanley' => 'Atlantic/Stanley',
                                        'Australia/Adelaide' => 'Australia/Adelaide',
                                        'Australia/Brisbane' => 'Australia/Brisbane',
                                        'Australia/Broken_Hill' => 'Australia/Broken_Hill',
                                        'Australia/Darwin' => 'Australia/Darwin',
                                        'Australia/Hobart' => 'Australia/Hobart',
                                        'Australia/Lindeman' => 'Australia/Lindeman',
                                        'Australia/Lord_Howe' => 'Australia/Lord_Howe',
                                        'Australia/Melbourne' => 'Australia/Melbourne',
                                        'Australia/Perth' => 'Australia/Perth',
                                        'Australia/Sydney' => 'Australia/Sydney',
                                        'Europe/Amsterdam' => 'Europe/Amsterdam',
                                        'Europe/Andorra' => 'Europe/Andorra',
                                        'Europe/Athens' => 'Europe/Athens',
                                        'Europe/Belfast' => 'Europe/Belfast',
                                        'Europe/Belgrade' => 'Europe/Belgrade',
                                        'Europe/Berlin' => 'Europe/Berlin',
                                        'Europe/Bratislava' => 'Europe/Bratislava',
                                        'Europe/Brussels' => 'Europe/Brussels',
                                        'Europe/Bucharest' => 'Europe/Bucharest',
                                        'Europe/Budapest' => 'Europe/Budapest',
                                        'Europe/Chisinau' => 'Europe/Chisinau',
                                        'Europe/Copenhagen' => 'Europe/Copenhagen',
                                        'Europe/Dublin' => 'Europe/Dublin',
                                        'Europe/Gibraltar' => 'Europe/Gibraltar',
                                        'Europe/Helsinki' => 'Europe/Helsinki',
                                        'Europe/Istanbul' => 'Europe/Istanbul',
                                        'Europe/Kaliningrad' => 'Europe/Kaliningrad',
                                        'Europe/Kiev' => 'Europe/Kiev',
                                        'Europe/Lisbon' => 'Europe/Lisbon',
                                        'Europe/Ljubljana' => 'Europe/Ljubljana',
                                        'Europe/London' => 'Europe/London',
                                        'Europe/Luxembourg' => 'Europe/Luxembourg',
                                        'Europe/Madrid' => 'Europe/Madrid',
                                        'Europe/Malta' => 'Europe/Malta',
                                        'Europe/Minsk' => 'Europe/Minsk',
                                        'Europe/Monaco' => 'Europe/Monaco',
                                        'Europe/Moscow' => 'Europe/Moscow',
                                        'Europe/Oslo' => 'Europe/Oslo',
                                        'Europe/Paris' => 'Europe/Paris',
                                        'Europe/Prague' => 'Europe/Prague',
                                        'Europe/Riga' => 'Europe/Riga',
                                        'Europe/Rome' => 'Europe/Rome',
                                        'Europe/Samara' => 'Europe/Samara',
                                        'Europe/San_Marino' => 'Europe/San_Marino',
                                        'Europe/Sarajevo' => 'Europe/Sarajevo',
                                        'Europe/Simferopol' => 'Europe/Simferopol',
                                        'Europe/Skopje' => 'Europe/Skopje',
                                        'Europe/Sofia' => 'Europe/Sofia',
                                        'Europe/Stockholm' => 'Europe/Stockholm',
                                        'Europe/Tallinn' => 'Europe/Tallinn',
                                        'Europe/Tirane' => 'Europe/Tirane',
                                        'Europe/Vaduz' => 'Europe/Vaduz',
                                        'Europe/Vatican' => 'Europe/Vatican',
                                        'Europe/Vienna' => 'Europe/Vienna',
                                        'Europe/Vilnius' => 'Europe/Vilnius',
                                        'Europe/Warsaw' => 'Europe/Warsaw',
                                        'Europe/Zagreb' => 'Europe/Zagreb',
                                        'Europe/Zurich' => 'Europe/Zurich',
                                        'Indian/Antananarivo' => 'Indian/Antananarivo',
                                        'Indian/Chagos' => 'Indian/Chagos',
                                        'Indian/Christmas' => 'Indian/Christmas',
                                        'Indian/Cocos' => 'Indian/Cocos',
                                        'Indian/Comoro' => 'Indian/Comoro',
                                        'Indian/Kerguelen' => 'Indian/Kerguelen',
                                        'Indian/Mahe' => 'Indian/Mahe',
                                        'Indian/Maldives' => 'Indian/Maldives',
                                        'Indian/Mauritius' => 'Indian/Mauritius',
                                        'Indian/Mayotte' => 'Indian/Mayotte',
                                        'Indian/Reunion' => 'Indian/Reunion',
                                        'Pacific/Apia' => 'Pacific/Apia',
                                        'Pacific/Auckland' => 'Pacific/Auckland',
                                        'Pacific/Chatham' => 'Pacific/Chatham',
                                        'Pacific/Easter' => 'Pacific/Easter',
                                        'Pacific/Efate' => 'Pacific/Efate',
                                        'Pacific/Enderbury' => 'Pacific/Enderbury',
                                        'Pacific/Fakaofo' => 'Pacific/Fakaofo',
                                        'Pacific/Fiji' => 'Pacific/Fiji',
                                        'Pacific/Funafuti' => 'Pacific/Funafuti',
                                        'Pacific/Galapagos' => 'Pacific/Galapagos',
                                        'Pacific/Gambier' => 'Pacific/Gambier',
                                        'Pacific/Guadalcanal' => 'Pacific/Guadalcanal',
                                        'Pacific/Guam' => 'Pacific/Guam',
                                        'Pacific/Honolulu' => 'Pacific/Honolulu',
                                        'Pacific/Johnston' => 'Pacific/Johnston',
                                        'Pacific/Kiritimati' => 'Pacific/Kiritimati',
                                        'Pacific/Kosrae' => 'Pacific/Kosrae',
                                        'Pacific/Kwajalein' => 'Pacific/Kwajalein',
                                        'Pacific/Majuro' => 'Pacific/Majuro',
                                        'Pacific/Marquesas' => 'Pacific/Marquesas',
                                        'Pacific/Midway' => 'Pacific/Midway',
                                        'Pacific/Nauru' => 'Pacific/Nauru',
                                        'Pacific/Niue' => 'Pacific/Niue',
                                        'Pacific/Norfolk' => 'Pacific/Norfolk',
                                        'Pacific/Noumea' => 'Pacific/Noumea',
                                        'Pacific/Pago_Pago' => 'Pacific/Pago_Pago',
                                        'Pacific/Palau' => 'Pacific/Palau',
                                        'Pacific/Pitcairn' => 'Pacific/Pitcairn',
                                        'Pacific/Ponape' => 'Pacific/Ponape',
                                        'Pacific/Port_Moresby' => 'Pacific/Port_Moresby',
                                        'Pacific/Rarotonga' => 'Pacific/Rarotonga',
                                        'Pacific/Saipan' => 'Pacific/Saipan',
                                        'Pacific/Tahiti' => 'Pacific/Tahiti',
                                        'Pacific/Tarawa' => 'Pacific/Tarawa',
                                        'Pacific/Tongatapu' => 'Pacific/Tongatapu',
                                        'Pacific/Truk' => 'Pacific/Truk',
                                        'Pacific/Wake' => 'Pacific/Wake',
                                        'Pacific/Wallis' => 'Pacific/Wallis',
                                        'Pacific/Yap' => 'Pacific/Yap',
                                    );

    /**
     * Gets the timezone list
     *
     * @return array of timezones
     */
    public static function getTimezoneList()
    {
        return self::$_timezoneList;
    }
    
}