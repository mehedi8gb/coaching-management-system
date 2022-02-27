<?php

namespace Database\Seeders;

use App\SmStaff;
use App\SmStaffAttendence;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class sm_staff_attendencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        SmStaffAttendence::query()->truncate();
        $staffs = SmStaff::where('school_id',1)->get(['id','user_id']);
        $days = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $status = ['P','L','A'];
        for ($i = 1; $i <= $days; $i++) {
            foreach ($staffs as $staff) {
                if ($i <= 9) {
                    $d = '0' . $i;
                }
                $date = date('Y') . '-' . date('m') . '-' . $d;                    

                $sa = new SmStaffAttendence;
                $sa->staff_id = $staff->id;
                $sa->attendence_type = array_rand($status);
                $sa->notes = 'Sample Attendance for Staff';
                $sa->attendence_date = $date;
                $sa->school_id = 1;
                $sa->academic_id = 1;
                $sa->save();
            }
        }

    }
}
