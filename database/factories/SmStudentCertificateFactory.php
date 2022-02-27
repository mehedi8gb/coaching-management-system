<?php

namespace Database\Factories;

use App\Models\Model;
use App\SmStudentCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmStudentCertificateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmStudentCertificate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'                =>'Certificate in Technical Communication (PCTC)',
            'header_left_text'    => 'Since 2020',
            'date'                => '2020-05-17',
            'body'                => 'Earning my UCR Extension professional certificate is one of the most beneficial things I\'ve  done for my career. Before even completing the program, I was contacted twice by companies who were interested in hiring me as a technical writer. This program helped me reach my career goals in a very short time',
            'footer_left_text'    => 'Advisor Signature',
            'footer_center_text'  => 'Instructor Signature',
            'footer_right_text' => 'Principal Signature',
            'student_photo'       => 0,
            'file'                => 'public/uploads/certificate/c.jpg',
        ];
    }
}
