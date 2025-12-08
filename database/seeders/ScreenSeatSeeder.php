<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seat;

class ScreenSeatSeeder extends Seeder
{
    public function run(): void
    {
        // CHANGE THIS to your real screen ID
        $screenId = 1;

        // Rows A–K (11 rows)
        $rows = range('A', 'K');

        // 20 seats per row
        $seatsPerRow = 20;

        foreach ($rows as $row) {
            for ($num = 1; $num <= $seatsPerRow; $num++) {

                Seat::create([
                    'screen_id'   => $screenId,
                    'row_label'   => $row,
                    'seat_number' => $num,
                    'seat_code'   => $row . $num,
                    'status'      => 'active',
                    'metadata'    => null,
                ]);
            }
        }

        echo "✔ 220 seats created for screen ID {$screenId}\n";
    }
}
