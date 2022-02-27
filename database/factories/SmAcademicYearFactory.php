<?php

namespace Database\Factories;

use App\SmAcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmAcademicYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmAcademicYear::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $year = $this->faker->year(now());
        $starting_date = $year . '-01-01';
        $ending_date = $year . '-12-31';
        return [
            'year' => $year,
            'title' => 'Academic Year ' . $year,
            'starting_date' => $starting_date,
            'ending_date' => $ending_date,
        ];
    }
}
