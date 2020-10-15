<?php

use Illuminate\Database\Seeder;
use App\Models\State;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        \DB::table('states')->delete();

        State::create([
            'name' => 'Alabama',
            'abbr' => 'AL',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Montana',
            'abbr' => 'MT',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Alaska',
            'abbr' => 'AK',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Nebraska',
            'abbr' => 'NE',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Arizona',
            'abbr' => 'AZ',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Nevada',
            'abbr' => 'NV',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Arkansas',
            'abbr' => 'AR',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'New Hampshire',
            'abbr' => 'NH',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'California',
            'abbr' => 'CA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'New Jersey',
            'abbr' => 'NJ',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Colorado',
            'abbr' => 'CO',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'New Mexico',
            'abbr' => 'NM',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Connecticut',
            'abbr' => 'CT',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'New York',
            'abbr' => 'NY',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Delaware',
            'abbr' => 'DE',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'North Carolina',
            'abbr' => 'NC',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Florida',
            'abbr' => 'FL',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'North Dakota',
            'abbr' => 'ND',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Georgia',
            'abbr' => 'GA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Ohio',
            'abbr' => 'OH',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Hawaii',
            'abbr' => 'HI',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Oklahoma',
            'abbr' => 'OK',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Idaho',
            'abbr' => 'ID',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Oregon',
            'abbr' => 'OR',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Illinois',
            'abbr' => 'IL',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Pennsylvania',
            'abbr' => 'PA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Indiana',
            'abbr' => 'IN',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Rhode Island',
            'abbr' => 'RI',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Iowa',
            'abbr' => 'IA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'South Carolina',
            'abbr' => 'SC',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Kansas',
            'abbr' => 'KS',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'South Dakota',
            'abbr' => 'SD',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Kentucky',
            'abbr' => 'KY',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Tennessee',
            'abbr' => 'TN',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Louisiana',
            'abbr' => 'LA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Texas',
            'abbr' => 'TX',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Maine',
            'abbr' => 'ME',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Utah',
            'abbr' => 'UT',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Maryland',
            'abbr' => 'MD',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Vermont',
            'abbr' => 'VT',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Massachusetts',
            'abbr' => 'MA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Virginia',
            'abbr' => 'VA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Michigan',
            'abbr' => 'MI',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Washington',
            'abbr' => 'WA',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Minnesota',
            'abbr' => 'MN',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'West Virginia',
            'abbr' => 'WV',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Mississippi',
            'abbr' => 'MS',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Wisconsin',
            'abbr' => 'WI',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Missouri',
            'abbr' => 'MO',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Wyoming',
            'abbr' => 'WY',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Abra',
            'abbr' => 'ABR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Agusan del Norte',
            'abbr' => 'AGN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Agusan del Sur',
            'abbr' => 'AGS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Aklan',
            'abbr' => 'AKL',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Albay',
            'abbr' => 'ALB',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Antique',
            'abbr' => 'ANT',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Apayao',
            'abbr' => 'APA',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Aurora',
            'abbr' => 'AUR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Basilan',
            'abbr' => 'BAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Bataan',
            'abbr' => 'BAN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Batanes',
            'abbr' => 'BTN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Batangas',
            'abbr' => 'BTG',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Benguet',
            'abbr' => 'BEN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Biliran',
            'abbr' => 'BIL',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Bohol',
            'abbr' => 'BOH',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Bukidnon',
            'abbr' => 'BUK',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Bulacan',
            'abbr' => 'BUL',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Cagayan',
            'abbr' => 'CAG',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Camarines Norte',
            'abbr' => 'CAN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Camarines Sur',
            'abbr' => 'CAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Camiguin',
            'abbr' => 'CAM',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Capiz',
            'abbr' => 'CAP',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Catanduanes',
            'abbr' => 'CAT',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Cavite',
            'abbr' => 'CAV',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Cebu',
            'abbr' => 'CEB',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Compostela Valley',
            'abbr' => 'COM',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Cotabato',
            'abbr' => 'NCO',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Davao del Norte',
            'abbr' => 'DAV',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Davao del Sur',
            'abbr' => 'DAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Davao Occidental',
            'abbr' => 'DVO',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Davao Oriental',
            'abbr' => 'DAO',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Dinagat Islands',
            'abbr' => 'DIN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Eastern Samar',
            'abbr' => 'EAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Guimaras',
            'abbr' => 'GUI',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Ifugao',
            'abbr' => 'IFU',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Ilocos Norte',
            'abbr' => 'ILN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Ilocos Sur',
            'abbr' => 'ILS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Iloilo',
            'abbr' => 'ILI',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Isabela',
            'abbr' => 'ISA',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Kalinga',
            'abbr' => 'KAL',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'La Union',
            'abbr' => 'LUN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Laguna',
            'abbr' => 'LAG',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Lanao del Norte',
            'abbr' => 'LAN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Lanao del Sur',
            'abbr' => 'LAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Leyte',
            'abbr' => 'LEY',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Maguindanao',
            'abbr' => 'MAG',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Marinduque',
            'abbr' => 'MAD',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Masbate',
            'abbr' => 'MAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Misamis Occidental',
            'abbr' => 'MSC',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Misamis Oriental',
            'abbr' => 'MSR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Mountain Province',
            'abbr' => 'MOU',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Negros Occidental',
            'abbr' => 'NEC',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Negros Oriental',
            'abbr' => 'NER',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Northern Samar',
            'abbr' => 'NSA',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Nueva Ecija',
            'abbr' => 'NUE',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Nueva Vizcaya',
            'abbr' => 'NUV',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Occidental Mindoro',
            'abbr' => 'MDC',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Oriental Mindoro',
            'abbr' => 'MDR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Palawa',
            'abbr' => 'PLW',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Pampanga',
            'abbr' => 'PAM',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Pangasinan',
            'abbr' => 'PAN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Quezon',
            'abbr' => 'QUE',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Quirino',
            'abbr' => 'QUI',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Rizal',
            'abbr' => 'RIZ',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Romblon',
            'abbr' => 'ROM',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Samar',
            'abbr' => 'WSA',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Sarangani',
            'abbr' => 'SAR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Siquijor',
            'abbr' => 'SIG',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Sorsogon',
            'abbr' => 'SOR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'South Cotabato',
            'abbr' => 'SCO',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Southern Leyte',
            'abbr' => 'SLE',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Sultan Kudarat',
            'abbr' => 'SUK',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Sulu',
            'abbr' => 'SLU',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Surigao del Norte',
            'abbr' => 'SUN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Surigao del Sur',
            'abbr' => 'SUR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Tarlac',
            'abbr' => 'TAR',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Tawi-Tawi',
            'abbr' => 'TAW',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Zambales',
            'abbr' => 'ZMB',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Zamboanga del Norte',
            'abbr' => 'ZAN',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Zamboanga del Sur',
            'abbr' => 'ZAS',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Zamboanga Sibugay',
            'abbr' => 'ZSI',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Metro Manila',
            'abbr' => 'MM',
            'country' => 'PH'
        ]);
        State::create([
            'name' => 'Qinghai',
            'abbr' => 'QH',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Sichuan',
            'abbr' => 'SC',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Gansu',
            'abbr' => 'GS',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Heilongjiang',
            'abbr' => 'HL',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Yunnan',
            'abbr' => 'YN',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Hunan',
            'abbr' => 'HN',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Shaanxi',
            'abbr' => 'SN',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Hebei',
            'abbr' => 'HE',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Jilin',
            'abbr' => 'JL',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Hubei',
            'abbr' => 'HB',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Guangdong',
            'abbr' => 'GD',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Guizhou',
            'abbr' => 'GZ',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Jiangxi',
            'abbr' => 'JX',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Henan',
            'abbr' => 'HA',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Shanxi',
            'abbr' => 'SX',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Shandong',
            'abbr' => 'SD',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Liaoning',
            'abbr' => 'LN',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Anhui',
            'abbr' => 'AH',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Fujian',
            'abbr' => 'FJ',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Jiangsu',
            'abbr' => 'JS',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Taiwan',
            'abbr' => 'TW',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Hainan',
            'abbr' => 'HI',
            'country' => 'CN'
        ]);
    }
}
