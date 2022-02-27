<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmBankPaymentSlip extends Model
{
    use HasFactory;
    public function studentInfo(){
    	return $this->belongsTo('App\SmStudent', 'student_id', 'id');
    }

    public function feesType(){
    	return $this->belongsTo('App\SmFeesType', 'fees_type_id', 'id');
    }
}
