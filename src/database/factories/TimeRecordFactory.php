<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TimeRecord;


class TimeRecordFactory extends Factory
{
    protected $model = TimeRecord::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                $user = \App\Models\User::inRandomOrder()->first();
                return $user->id;
            },
            'date' => $this->faker->date,
            'start_time' => $this->faker->time,
            'end_time' => $this->faker->time,
            'break_start' => $this->faker->time,
            'break_end' => $this->faker->time,
        ];
    }
}
