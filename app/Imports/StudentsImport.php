<?php

namespace App\Imports;

use App\StudentBulkTemporary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class StudentsImport implements ToModel, WithStartRow, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new StudentBulkTemporary([
          "admission_number" => @$row['admission_number'],
          "roll_no" => @$row['roll_no'],
          "first_name" => @$row['first_name'],
          "last_name" => @$row['last_name'],
          "date_of_birth" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_birth'])->format('Y-m-d'),
          "religion" => @$row['religion'],
          "gender" => @$row['gender'],
          "caste" => @$row['caste'],
          "mobile" => @$row['mobile'],
          "email" => @$row['email'],
          "admission_date" => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['admission_date'])->format('Y-m-d'),
          "blood_group" => @$row['blood_group'],
          "height" => @$row['height'],
          "weight" => @$row['weight'],
          "father_name" => @$row['father_name'],
          "father_phone" => @$row['father_phone'],
          "father_occupation" => @$row['father_occupation'],
          "mother_name" => @$row['mother_name'],
          "mother_phone" => @$row['mother_phone'],
          "mother_occupation" => @$row['mother_occupation'],
          "guardian_name" => @$row['guardian_name'],
          "guardian_relation" => @$row['guardian_relation'],
          "guardian_email" => @$row['guardian_email'],
          "guardian_phone" => @$row['guardian_phone'],
          "guardian_occupation" => @$row['guardian_occupation'],
          "guardian_address" => @$row['guardian_address'],
          "current_address" => @$row['current_address'],
          "permanent_address" => @$row['permanent_address'],
          "bank_account_no" => @$row['bank_account_no'],
          "bank_name" => @$row['bank_name'],
          "national_identification_no" => @$row['national_identification_no'],
          "local_identification_no" => @$row['local_identification_no'],
          "previous_school_details" => @$row['previous_school_details'],
          "note" => @$row['note'],
          "user_id" => Auth::user()->id
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function headingRow(): int
    {
        return 1;
    }
}