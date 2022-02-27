<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmStudentTakeOnlineExam extends Model
{
    public static function submittedAnswer($exam_id, $s_id){ 
		try {
			return SmStudentTakeOnlineExam::where('online_exam_id', $exam_id)->where('student_id', $s_id)->first();
		} catch (\Exception $e) {
			$data=[];
			return $data;
		} 
    }

    public function answeredQuestions(){
    	return $this->hasMany('App\SmStudentTakeOnlineExamQuestion', 'take_online_exam_id', 'id');
    }

    public function onlineExam(){
    	return $this->belongsTo('App\SmOnlineExam', 'online_exam_id', 'id');
    }

    
}
