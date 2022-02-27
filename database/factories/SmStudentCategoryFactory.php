<?php

namespace Database\Factories;

use App\Models\Model;
use App\SmStudentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmStudentCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmStudentCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public $i=0;
    public function definition()
    {
        return [
            'category_name'=> $this->faker->colorName .$this->i++,
        ];
    }
}
