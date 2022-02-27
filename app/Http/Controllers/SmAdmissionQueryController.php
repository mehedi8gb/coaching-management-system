<?php

namespace App\Http\Controllers;

use App\SmClass;
use App\SmSetupAdmin;
use App\SmAdmissionQuery;
use Illuminate\Http\Request;
use App\SmAdmissionQueryFollowup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmAdmissionQueryController extends Controller
{

	public function __construct()
	{
		$this->middleware('PM');
	}


	public function index()
	{
		try {
			$admission_queries = SmAdmissionQuery::orderBy('id', 'DESC')->where('school_id', Auth::user()->school_id)->get();
			$classes = SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			return view('backEnd.admin.admission_query', compact('admission_queries', 'references', 'classes', 'sources'));
		} catch (\Exception $e) {
			Log::info($e->getMessage());
			dd($e->getMessage());
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function admissionQueryStore(Request $request)
	{

		$user = Auth::user();
		$request->validate([
			'phone' => 'required',
			'class' => 'required',
			'date' => 'required|date',
			'next_follow_up_date' => 'required|date|after:date'
		]);

		try {
			$admission_query = new SmAdmissionQuery();
			$admission_query->name = $request->name;
			$admission_query->phone = $request->phone;
			$admission_query->email = $request->email;
			$admission_query->address = $request->address;
			$admission_query->description = $request->description;
			$admission_query->date = date('Y-m-d', strtotime($request->date));
			$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
			$admission_query->assigned = $request->assigned;
			$admission_query->reference = $request->reference;
			$admission_query->source = $request->source;
			$admission_query->class = $request->class;
			$admission_query->no_of_child = $request->no_of_child;
			$admission_query->created_by = $user->id;
			$admission_query->school_id = Auth::user()->school_id;
			$result = $admission_query->save();


			Toastr::success('Operation successful', 'Success');
			return redirect()->back();
		} catch (\Exception $e) {
			dd($e->getMessage());
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function admissionQueryEdit($id)
	{
		try {
			$admission_query = SmAdmissionQuery::find($id);
			$classes = SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			return view('backEnd.admin.admission_query_edit', compact('admission_query', 'references', 'classes', 'sources'));
		} catch (\Exception $e) {
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function admissionQueryUpdate(Request $request)
	{
		try {
			$admission_query = SmAdmissionQuery::find($request->id);
			$admission_query->name = $request->name;
			$admission_query->phone = $request->phone;
			$admission_query->email = $request->email;
			$admission_query->address = $request->address;
			$admission_query->description = $request->description;
			$admission_query->date = date('Y-m-d', strtotime($request->date));
			$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
			$admission_query->assigned = $request->assigned;
			if ($request->reference) {
				$admission_query->reference = $request->reference;
			}
			$admission_query->source = $request->source;
			$admission_query->class = $request->class;
			$admission_query->no_of_child = $request->no_of_child;
			$admission_query->school_id = Auth::user()->school_id;
			$result = $admission_query->save();
			if ($result) {
				Toastr::success('Operation successful', 'Success');
				return redirect()->back();
			} else {
				Toastr::error('Operation Failed', 'Failed');
				return redirect()->back();
			}
		} catch (\Exception $e) {
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function addQuery($id)
	{
		try {
			$admission_query = SmAdmissionQuery::find($id);
			$follow_up_lists = SmAdmissionQueryFollowup::where('admission_query_id', $id)->get();
			$classes = SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			return view('backEnd.admin.add_query', compact('admission_query', 'follow_up_lists', 'references', 'classes', 'sources'));
		} catch (\Exception $e) {
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function queryFollowupStore(Request $request)
	{
		$request->validate([
			'next_follow_up_date' => 'required',
			'response' => 'required'
		]);
		$user = Auth::user();

		DB::beginTransaction();
		try {
			$admission_query = SmAdmissionQuery::find($request->id);
			$admission_query->follow_up_date = date('Y-m-d', strtotime($request->follow_up_date));
			$admission_query->next_follow_up_date = date('Y-m-d', strtotime($request->next_follow_up_date));
			$admission_query->active_status = $request->status;
			$admission_query->school_id = Auth::user()->school_id;
			$admission_query->save();
			$admission_query->toArray();


			$follow_up = new SmAdmissionQueryFollowup();
			$follow_up->admission_query_id = $admission_query->id;
			$follow_up->response = $request->response;
			$follow_up->note = $request->note;
			$follow_up->created_by = $user->id;
			$follow_up->school_id = Auth::user()->school_id;
			$follow_up->save();


			DB::commit();
			Toastr::success('Operation successful', 'Success');
			return redirect()->back();
			// return redirect()->back()->with('message-success', 'Follow up report has been forwarded successfully');
		} catch (\Exception $e) {
			DB::rollback();
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
			// return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
		}
	}

	public function deleteFollowUp($id)
	{
		try {
			$result = SmAdmissionQueryFollowup::destroy($id);
			if ($result) {
				Toastr::success('Operation successful', 'Success');
				return redirect()->back();
			} else {
				Toastr::error('Operation Failed', 'Failed');
				return redirect()->back();
			}
		} catch (\Exception $e) {
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function admissionQueryDelete(Request $request)
	{
		DB::beginTransaction();
		try {
			$admission_query = SmAdmissionQuery::find($request->id);
			SmAdmissionQueryFollowup::where('admission_query_id', $admission_query->id)->delete();
			$admission_query->delete();
			DB::commit();
			Toastr::success('Operation successful', 'Success');
			return redirect()->back();
		} catch (\Exception $e) {
			DB::rollback();
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}

	public function admissionQuerySearch(Request $request)
	{
		//return $request;
		$validatedData = $request->validate([
			'date_from' => 'required|date',
			'date_to' => 'required|date|after:date_from'
		]);


		try {
			$date_from = date('Y-m-d', strtotime($request->date_from));
			$date_to = date('Y-m-d', strtotime($request->date_to));

			$admission_queries = SmAdmissionQuery::query();

			if ($request->date_from != "" && $request->date_to) {
				$admission_queries->where('date', '>=', $date_from)->where('date', '<=', $date_to);
			}
			if ($request->source != "") {
				$admission_queries->where('source', $request->source);
			}
			if ($request->status != "") {
				$admission_queries->where('active_status', $request->status);
			}
			$admission_queries = $admission_queries->where('school_id', Auth::user()->school_id)->get();

			$date_from = $request->date_from;
			$date_to = $request->date_to;
			$source_id = $request->source;
			$status_id = $request->status;



			$classes = SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$references = SmSetupAdmin::where('type', 4)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			$sources = SmSetupAdmin::where('type', 3)->where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
			return view('backEnd.admin.admission_query', compact('admission_queries', 'references', 'classes', 'sources', 'date_from', 'date_to', 'source_id', 'status_id'));
		} catch (\Exception $e) {
			Toastr::error('Operation Failed', 'Failed');
			return redirect()->back();
		}
	}
}
