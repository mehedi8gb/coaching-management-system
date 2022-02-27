<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class tableList extends Model
{
	public static function getTableList($colunm_name_id, $id)
	{
		try{
			$db_name = env('DB_DATABASE', null);
			$table_list = DB::select("SELECT TABLE_NAME 
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE COLUMN_NAME ='$colunm_name_id'
				AND TABLE_SCHEMA='$db_name'");
			$tables = '';

 
			foreach ($table_list as $row) {

				$data_test = DB::table($row->TABLE_NAME)->select('*')->where($colunm_name_id, $id)->first();

				if($data_test != ""){



					$name = str_replace('sm_', '', $row->TABLE_NAME);
					$name = str_replace('_', ' ', $name);
					$name = ucfirst($name);
	
						$tables .= $name . ', ';
			
				}
			}
			
			return $tables;
		}catch(\Exception $e){
			$tables='hfg';
			return $tables;
		}
	}


	public static function ONLY_TABLE_LIST($id)
	{
		try{
			$db_name = env('DB_DATABASE', null);
			$table_list = DB::select("SELECT TABLE_NAME 
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE COLUMN_NAME ='$id'
				AND TABLE_SCHEMA='$db_name'");
			$tables = [];
			foreach ($table_list as $row) {
				$tables[] = $row->TABLE_NAME;
			}
			return $tables;

		}catch(\Exception $e){
			$tables=[];
			return $tables;
		}

	}
	
	public static function allTableList($column)
	{

		//this function not working 
		try {
			$db_name = env('DB_DATABASE', null);
			return $db_name;
		} catch (\Exception $e) {
			return $e;
		}

		$db_name = env('DB_DATABASE', null);
		$table_list = DB::select("SELECT TABLE_NAME 
		FROM INFORMATION_SCHEMA.COLUMNS
		WHERE COLUMN_NAME ='$column'
			AND TABLE_SCHEMA='$db_name'");
		$tables = [];
		foreach ($table_list as $row) {
			$tables[] = $row->TABLE_NAME;
		}
		return $tables;
	}
}
