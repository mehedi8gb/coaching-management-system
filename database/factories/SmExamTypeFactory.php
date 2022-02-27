<?php

namespace Database\Factories;

use App\SmExamType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmExamTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmExamType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' =>$this->faker->word,
        ];
    }
}
