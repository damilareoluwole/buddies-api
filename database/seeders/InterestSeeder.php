<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            [
                "name" => "Football"
            ],
            [
                "name"=> "Basketball"
            ],
            [
                "name"=> "Ice Hockey"
            ],
            [
                "name" => "Motorsports,"
            ],
            [
                "name"=> "Brandy"
            ],
            [
                "name"=> "Rugby"
            ],
            [
                "name"=> "Skiing"
            ],
            [
                "name"=> "Shooting"
            ]
        ];

        foreach ($interests as $interest) {
            if (Interest::where('name', $interest['name'])->exists()) {
                continue;
            }

            Interest::create($interest);
        }
    }
}