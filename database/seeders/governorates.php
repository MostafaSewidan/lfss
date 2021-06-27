<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class governorates extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $govs = 'Alexandria||Assiut||Aswan||Beheira||Bani Suef||Cairo||Daqahliya||Damietta||Fayyoum||Gharbiya||Giza||Helwan||Ismailia||Kafr El Sheikh||Luxor||Marsa Matrouh||Minya||Monofiya||New Valley||North Sinai||Port Said||Qalioubiya||Qena||Red Sea||Sharqiya||Sohag||South Sinai||Suez||Tanta';

        foreach (explode('||',$govs) as $gov) {
            Governorate::create(['name' =>$gov]);
        }
    }
}
