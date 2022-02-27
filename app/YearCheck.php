<?php

namespace App; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class YearCheck extends Model
{
    public static function getYear()
    {
        try { 
            $year = SmGeneralSettings::where('school_id',Auth::user()->school_id)->first(); 
            return $year->academic_Year->year;
        } catch (\Exception $e) {
            return date('Y');
        }
    }
}
