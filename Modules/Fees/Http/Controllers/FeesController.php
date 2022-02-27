<?php

namespace Modules\Fees\Http\Controllers;

use App\SmClass;
use App\SmSchool;
use App\SmStudent;
use App\Models\User;
use App\SmAddIncome;
use App\SmBankAccount;
use App\SmBankStatement;
use App\SmPaymentMethhod;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use App\SmPaymentGatewaySetting;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Fees\Entities\FmFeesType;
use Modules\Fees\Entities\FmFeesGroup;
use Modules\Fees\Entities\FmFeesWeaver;
use Modules\Fees\Entities\FmFeesInvoice;
use Illuminate\Support\Facades\Validator;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Requests\BankFeesPayment;
use Modules\Fees\Entities\FmFeesInvoiceSettings;
use Modules\Fees\Entities\FmFeesTransactionChield;

class FeesController extends Controller
{
    public function feesGroup()
    {
        $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get();
        return view('fees::feesGroup', compact('feesGroups'));
    }

    public function feesGroupStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:fm_fees_groups,name|max:100"
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $feesGroup = new FmFeesGroup();
            $feesGroup->name = $request->name;
            $feesGroup->description = $request->description;
            $feesGroup->school_id = Auth::user()->school_id;
            $feesGroup->academic_id = getAcademicId();
            $feesGroup->save();

            Toastr::success('Save Successful', 'Success');
            return redirect()->route('fees.fees-group');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesGroupEdit($id)
    {
        try {
            if (checkAdmin()) {
                $feesGroup = FmFeesGroup::find($id);
            } else {
                $feesGroup = FmFeesGroup::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
            }
            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();
            return view('fees::feesGroup', compact('feesGroup', 'feesGroups'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesGroupUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);

        $ifExistes = FmFeesGroup::where('name', $request->name)
            ->where('school_id', Auth::user()->school_id)
            ->where('id', '!=', $request->id)
            ->where('academic_id', getAcademicId())
            ->first();
        if ($ifExistes) {
            Toastr::Warning('Duplicate Name Found!', 'Warning');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (checkAdmin()) {
                $feesGroup = FmFeesGroup::find($request->id);
            } else {
                $feesGroup = FmFeesGroup::where('id', $request->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
            }
            $feesGroup->name = $request->name;
            $feesGroup->description = $request->description;
            $feesGroup->academic_id = getAcademicId();
            $feesGroup->save();

            Toastr::success('Update Successful', 'Success');
            return redirect()->route('fees.fees-group');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesGroupDelete(Request $request)
    {
        try {
            if (checkAdmin()) {
                FmFeesGroup::destroy($request->id);
            } else {
                FmFeesGroup::where('id', $request->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->delete();
            }
            Toastr::success('Delete Successful', 'Success');
            return redirect()->route('fees.fees-group');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesType()
    {
        $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get();

        $feesTypes = FmFeesType::where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get();

        return view('fees::feesType', compact('feesGroups', 'feesTypes'));
    }

    public function feesTypeStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|max:50|unique:fm_fees_types,name",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $feesType = new FmFeesType();
            $feesType->name = $request->name;
            $feesType->fees_group_id = $request->fees_group;
            $feesType->description = $request->description;
            $feesType->school_id = Auth::user()->school_id;
            $feesType->academic_id = getAcademicId();
            $feesType->save();

            Toastr::success('Save Successful', 'Success');
            return redirect()->route('fees.fees-type');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesTypeEdit($id)
    {
        try {
            if (checkAdmin()) {
                $feesType = FmFeesType::find($id);
            } else {
                $feesType = FmFeesType::where('id', $id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
            }
            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesTypes = FmFeesType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('fees::feesType', compact('feesGroups', 'feesTypes', 'feesType'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesTypeUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
        ]);

        $ifExistes = FmFeesType::where('id', '!=', $request->id)
            ->where('school_id', Auth::user()->school_id)
            ->where('name', $request->name)
            ->where('fees_group_id', $request->fees_group)
            ->where('academic_id', getAcademicId())
            ->first();

        if ($ifExistes) {
            Toastr::Warning('Duplicate Name Found!', 'Warning');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if (checkAdmin()) {
                $feesType = FmFeesType::find($request->id);
            } else {
                $feesType = FmFeesType::where('id', $request->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
            }
            $feesType->name = $request->name;
            $feesType->fees_group_id = $request->fees_group;
            $feesType->description = $request->description;
            $feesType->save();

            Toastr::success('Update Successful', 'Success');
            return redirect()->route('fees.fees-type');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesTypeDelete(Request $request)
    {
        try {
            $feesGroupId = FmFeesType::find($request->id);
            $checkExistsData = FmFeesGroup::where('id', $feesGroupId->fees_group_id)->first();

            if (!$checkExistsData) {
                if (checkAdmin()) {
                    FmFeesType::destroy($request->id);
                } else {
                    FmFeesType::where('id', $request->id)
                        ->where('school_id', Auth::user()->school_id)
                        ->delete();
                }
                Toastr::success('Delete Successful', 'Success');
                return redirect()->route('fees.fees-type');
            } else {
                $msg = 'This Data Already Used In Fees Group Please Remove Those Data First';
                Toastr::warning($msg, 'Warning');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesInvoiceList()
    {
        $studentInvoices = FmFeesInvoice::where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get();

        return view('fees::feesInvoice.feesInvoiceList', compact('studentInvoices'));
    }

    public function feesInvoice()
    {
        try {
            $classes = SmClass::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesTypes = FmFeesType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $paymentMethods = SmPaymentMethhod::whereIn('method', ["Cash", "Cheque", "Bank"])
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $bankAccounts = SmBankAccount::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $invoiceSettings = FmFeesInvoiceSettings::where('school_id', Auth::user()->school_id)->first();

            return view('fees::feesInvoice.feesInvoice', compact('classes', 'feesGroups', 'feesTypes', 'paymentMethods', 'bankAccounts', 'invoiceSettings'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function feesInvoiceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class' => 'required',
            'student' => 'required',
            'create_date' => 'required',
            'due_date' => 'required',
            'payment_status' => 'required',
            'payment_method' => 'required_if:payment_status,partial|required_if:payment_status,full',
            'bank' => 'required_if:payment_method,Bank',
            'fees_type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->student != "all_student") {
                $storeFeesInvoice = new FmFeesInvoice();
                $storeFeesInvoice->class_id = $request->class;
                $storeFeesInvoice->create_date = date('Y-m-d', strtotime($request->create_date));
                $storeFeesInvoice->due_date = date('Y-m-d', strtotime($request->due_date));
                $storeFeesInvoice->payment_status = $request->payment_status;
                $storeFeesInvoice->payment_method = $request->payment_method;
                $storeFeesInvoice->bank_id = $request->bank;
                $storeFeesInvoice->student_id = $request->student;
                $storeFeesInvoice->school_id = Auth::user()->school_id;
                $storeFeesInvoice->academic_id = getAcademicId();
                $storeFeesInvoice->save();
                $storeFeesInvoice->invoice_id = feesInvoiceNumber($storeFeesInvoice);
                $storeFeesInvoice->save();


                if ($request->paid_amount > 0) {
                    $storeTransaction = new FmFeesTransaction();
                    $storeTransaction->fees_invoice_id = $storeFeesInvoice->id;
                    $storeTransaction->payment_method = $request->payment_method;
                    $storeTransaction->bank_id = $request->bank;
                    $storeTransaction->student_id = $request->student;
                    $storeTransaction->user_id = Auth::user()->id;
                    // $storeTransaction->file = $file;
                    $storeTransaction->paid_status = 'approve';
                    $storeTransaction->school_id = Auth::user()->school_id;
                    $storeTransaction->academic_id = getAcademicId();
                    $storeTransaction->save();
                }

                foreach ($request->feesType as $key => $type) {
                    $storeFeesInvoiceChield = new FmFeesInvoiceChield();
                    $storeFeesInvoiceChield->fees_invoice_id = $storeFeesInvoice->id;
                    $storeFeesInvoiceChield->fees_type = $type;
                    $storeFeesInvoiceChield->amount = $request->amount[$key];
                    $storeFeesInvoiceChield->weaver = $request->weaver[$key];
                    $storeFeesInvoiceChield->sub_total = $request->sub_total[$key];
                    $storeFeesInvoiceChield->note = $request->note[$key];
                    if ($request->paid_amount > 0) {
                        $storeFeesInvoiceChield->paid_amount = $request->paid_amount[$key];
                        $storeFeesInvoiceChield->due_amount = $request->sub_total[$key] - $request->paid_amount[$key];
                    } else {
                        $storeFeesInvoiceChield->due_amount = $request->sub_total[$key];
                    }
                    $storeFeesInvoiceChield->school_id = Auth::user()->school_id;
                    $storeFeesInvoiceChield->academic_id = getAcademicId();
                    $storeFeesInvoiceChield->save();

                    if ($request->paid_amount > 0) {
                        $storeTransactionChield = new FmFeesTransactionChield();
                        $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                        $storeTransactionChield->fees_type = $type;
                        $storeTransactionChield->weaver = $request->weaver[$key];
                        $storeTransactionChield->paid_amount = $request->paid_amount[$key];
                        $storeTransactionChield->note = $request->note[$key];
                        $storeTransactionChield->school_id = Auth::user()->school_id;
                        $storeTransactionChield->academic_id = getAcademicId();
                        $storeTransactionChield->save();

                        // Income
                        $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                        $income_head = generalSetting();

                        $add_income = new SmAddIncome();
                        $add_income->name = 'Fees Collect';
                        $add_income->date = date('Y-m-d');
                        $add_income->amount = $request->paid_amount[$key];
                        $add_income->fees_collection_id = $storeTransaction->id;
                        $add_income->active_status = 1;
                        $add_income->income_head_id = $income_head->income_head_id;
                        $add_income->payment_method_id = $payment_method->id;
                        $add_income->created_by = Auth()->user()->id;
                        $add_income->school_id = Auth::user()->school_id;
                        $add_income->academic_id = getAcademicId();
                        $add_income->save();

                        // Bank
                        if ($request->payment_method == "Bank") {
                            $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                            $bank = SmBankAccount::where('id', $request->bank)
                                ->where('school_id', Auth::user()->school_id)
                                ->first();
                            $after_balance = $bank->current_balance + $request->paid_amount[$key];

                            $bank_statement = new SmBankStatement();
                            $bank_statement->amount = $request->paid_amount[$key];
                            $bank_statement->after_balance = $after_balance;
                            $bank_statement->type = 1;
                            $bank_statement->details = "Fees Payment";
                            $bank_statement->item_sell_id = $storeTransaction->id;
                            $bank_statement->payment_date = date('Y-m-d');
                            $bank_statement->bank_id = $request->bank;
                            $bank_statement->school_id = Auth::user()->school_id;
                            $bank_statement->payment_method = $payment_method->id;
                            $bank_statement->save();

                            $current_balance = SmBankAccount::find($request->bank);
                            $current_balance->current_balance = $after_balance;
                            $current_balance->update();
                        }
                    }

                    $storeWeaver = new FmFeesWeaver();
                    $storeWeaver->fees_invoice_id = $storeFeesInvoice->id;
                    $storeWeaver->fees_type = $type;
                    $storeWeaver->student_id = $request->student;
                    $storeWeaver->weaver = $request->weaver[$key];
                    $storeWeaver->note = $request->note[$key];
                    $storeWeaver->school_id = Auth::user()->school_id;
                    $storeWeaver->academic_id = getAcademicId();
                    $storeWeaver->save();
                }

                //Notification
                $student = SmStudent::with('parents')->find($request->student);
                sendNotification("Fees Assign", null, $student->user_id, 2);
                sendNotification("Fees Assign", null, $student->parents->user_id, 3);
            } else {
                $allStudents = SmStudent::with('parents')->where('class_id', $request->class)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->get();

                foreach ($allStudents as $key => $student) {
                    $storeFeesInvoice = new FmFeesInvoice();
                    $storeFeesInvoice->class_id = $request->class;
                    $storeFeesInvoice->create_date = date('Y-m-d', strtotime($request->create_date));
                    $storeFeesInvoice->due_date = date('Y-m-d', strtotime($request->due_date));
                    $storeFeesInvoice->payment_status = $request->payment_status;
                    $storeFeesInvoice->payment_method = $request->payment_method;
                    $storeFeesInvoice->bank_id = $request->bank;
                    $storeFeesInvoice->student_id = $student->id;
                    $storeFeesInvoice->school_id = Auth::user()->school_id;
                    $storeFeesInvoice->academic_id = getAcademicId();
                    $storeFeesInvoice->save();
                    $storeFeesInvoice->invoice_id = feesInvoiceNumber($storeFeesInvoice);
                    $storeFeesInvoice->save();

                    if ($request->paid_amount > 0) {
                        $storeTransaction = new FmFeesTransaction();
                        $storeTransaction->fees_invoice_id = $storeFeesInvoice->id;
                        $storeTransaction->payment_method = $request->payment_method;
                        $storeTransaction->bank_id = $request->bank;
                        $storeTransaction->student_id = $request->student;
                        $storeTransaction->user_id = Auth::user()->id;
                        // $storeTransaction->file = $file;
                        $storeTransaction->paid_status = 'approve';
                        $storeTransaction->school_id = Auth::user()->school_id;
                        $storeTransaction->academic_id = getAcademicId();
                        $storeTransaction->save();
                    }

                    foreach ($request->feesType as $key => $type) {
                        $storeFeesInvoiceChield = new FmFeesInvoiceChield();
                        $storeFeesInvoiceChield->fees_invoice_id = $storeFeesInvoice->id;
                        $storeFeesInvoiceChield->fees_type = $type;
                        $storeFeesInvoiceChield->amount = $request->amount[$key];
                        $storeFeesInvoiceChield->weaver = $request->weaver[$key];
                        $storeFeesInvoiceChield->sub_total = $request->sub_total[$key];
                        $storeFeesInvoiceChield->note = $request->note[$key];
                        if ($request->paid_amount > 0) {
                            $storeFeesInvoiceChield->paid_amount = $request->paid_amount[$key];
                            $storeFeesInvoiceChield->due_amount = $request->sub_total[$key] - $request->paid_amount[$key];
                        } else {
                            $storeFeesInvoiceChield->due_amount = $request->sub_total[$key];
                        }
                        $storeFeesInvoiceChield->school_id = Auth::user()->school_id;
                        $storeFeesInvoiceChield->academic_id = getAcademicId();
                        $storeFeesInvoiceChield->save();

                        if ($request->paid_amount > 0) {
                            $storeTransactionChield = new FmFeesTransactionChield();
                            $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                            $storeTransactionChield->fees_type = $type;
                            $storeTransactionChield->weaver = $request->weaver[$key];
                            $storeTransactionChield->paid_amount = $request->paid_amount[$key];
                            $storeTransactionChield->note = $request->note[$key];
                            $storeTransactionChield->school_id = Auth::user()->school_id;
                            $storeTransactionChield->academic_id = getAcademicId();
                            $storeTransactionChield->save();

                            // Income
                            $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                            $income_head = generalSetting();

                            $add_income = new SmAddIncome();
                            $add_income->name = 'Fees Collect';
                            $add_income->date = date('Y-m-d');
                            $add_income->amount = $request->paid_amount[$key];
                            $add_income->fees_collection_id = $storeTransaction->id;
                            $add_income->active_status = 1;
                            $add_income->income_head_id = $income_head->income_head_id;
                            $add_income->payment_method_id = $payment_method->id;
                            $add_income->created_by = Auth()->user()->id;
                            $add_income->school_id = Auth::user()->school_id;
                            $add_income->academic_id = getAcademicId();
                            $add_income->save();

                            // Bank
                            if ($request->payment_method == "Bank") {
                                $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                                $bank = SmBankAccount::where('id', $request->bank)
                                    ->where('school_id', Auth::user()->school_id)
                                    ->first();
                                $after_balance = $bank->current_balance + $request->paid_amount[$key];

                                $bank_statement = new SmBankStatement();
                                $bank_statement->amount = $request->paid_amount[$key];
                                $bank_statement->after_balance = $after_balance;
                                $bank_statement->type = 1;
                                $bank_statement->details = "Fees Payment";
                                $bank_statement->item_sell_id = $storeTransaction->id;
                                $bank_statement->payment_date = date('Y-m-d');
                                $bank_statement->bank_id = $request->bank;
                                $bank_statement->school_id = Auth::user()->school_id;
                                $bank_statement->payment_method = $payment_method->id;
                                $bank_statement->save();

                                $current_balance = SmBankAccount::find($request->bank);
                                $current_balance->current_balance = $after_balance;
                                $current_balance->update();
                            }
                        }

                        $storeWeaver = new FmFeesWeaver();
                        $storeWeaver->fees_invoice_id = $storeFeesInvoice->id;
                        $storeWeaver->fees_type = $type;
                        $storeWeaver->student_id = $student->id;
                        $storeWeaver->weaver = $request->weaver[$key];
                        $storeWeaver->note = $request->note[$key];
                        $storeWeaver->school_id = Auth::user()->school_id;
                        $storeWeaver->academic_id = getAcademicId();
                        $storeWeaver->save();
                    }

                    //Notification
                    sendNotification("Fees Assign", null, $student->user_id, 2);
                    sendNotification("Fees Assign", null, $student->parents->user_id, 2);
                }
            }
            sendNotification("Fees Assign", null, 1, 1);
            Toastr::success('Store Successful', 'Success');
            return redirect()->route('fees.fees-invoice');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesInvoiceEdit($id)
    {
        try {
            // View Start
            $classes = SmClass::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesTypes = FmFeesType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $paymentMethods = SmPaymentMethhod::whereIn('method', ["Cash", "Cheque", "Bank"])
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $bankAccounts = SmBankAccount::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();
            // View End

            $invoiceSettings = FmFeesInvoiceSettings::where('school_id', Auth::user()->school_id)->first();

            $invoiceInfo = FmFeesInvoice::find($id);
            $invoiceDetails = FmFeesInvoiceChield::where('fees_invoice_id', $invoiceInfo->id)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $students = SmStudent::where('class_id', $invoiceInfo->class_id)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('fees::feesInvoice.feesInvoice', compact('classes', 'feesGroups', 'feesTypes', 'paymentMethods', 'bankAccounts', 'invoiceSettings', 'invoiceInfo', 'invoiceDetails', 'students'));

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesInvoiceUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class' => 'required',
            'student' => 'required',
            'create_date' => 'required',
            'due_date' => 'required',
            'payment_status' => 'required',
            'payment_method' => 'required_if:payment_status,partial|required_if:payment_status,full',
            'bank' => 'required_if:payment_method,Bank',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $storeFeesInvoice = FmFeesInvoice::find($request->id);
            $storeFeesInvoice->class_id = $request->class;
            $storeFeesInvoice->create_date = date('Y-m-d', strtotime($request->create_date));
            $storeFeesInvoice->due_date = date('Y-m-d', strtotime($request->due_date));
            $storeFeesInvoice->payment_status = $request->payment_status;
            $storeFeesInvoice->bank_id = $request->bank;
            $storeFeesInvoice->student_id = $request->student;
            $storeFeesInvoice->school_id = Auth::user()->school_id;
            $storeFeesInvoice->academic_id = getAcademicId();
            $storeFeesInvoice->update();

            FmFeesInvoiceChield::where('fees_invoice_id', $request->id)->delete();
            FmFeesWeaver::where('fees_invoice_id', $storeFeesInvoice->id)->delete();

            foreach ($request->feesType as $key => $type) {
                $storeFeesInvoiceChield = new FmFeesInvoiceChield();
                $storeFeesInvoiceChield->fees_invoice_id = $storeFeesInvoice->id;
                $storeFeesInvoiceChield->fees_type = $type;
                $storeFeesInvoiceChield->amount = $request->amount[$key];
                $storeFeesInvoiceChield->weaver = $request->weaver[$key];
                $storeFeesInvoiceChield->sub_total = $request->sub_total[$key];
                $storeFeesInvoiceChield->due_amount = $request->sub_total[$key];
                $storeFeesInvoiceChield->note = $request->note[$key];

                if ($request->paid_amount) {
                    $storeFeesInvoiceChield->paid_amount = $request->paid_amount[$key];
                }

                $storeFeesInvoiceChield->school_id = Auth::user()->school_id;
                $storeFeesInvoiceChield->academic_id = getAcademicId();
                $storeFeesInvoiceChield->save();

                $storeWeaver = new FmFeesWeaver();
                $storeWeaver->fees_invoice_id = $storeFeesInvoice->id;
                $storeWeaver->fees_type = $type;
                $storeWeaver->student_id = $request->student;
                $storeWeaver->weaver = $request->weaver[$key];
                $storeWeaver->note = $request->note[$key];
                $storeWeaver->school_id = Auth::user()->school_id;
                $storeWeaver->academic_id = getAcademicId();
                $storeWeaver->save();
            }

            //Notification
            $student = SmStudent::with('parents')->find($request->student);
            sendNotification("Fees Assign Update", null, $student->user_id, 2);
            sendNotification("Fees Assign Update", null, $student->parents->user_id, 3);
            Toastr::success('Update Successful', 'Success');
            return redirect()->route('fees.fees-invoice-list');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesInvoiceView($id, $state)
    {
        $generalSetting = SmGeneralSettings::where('school_id', Auth::user()->school_id)->first();
        $invoiceInfo = FmFeesInvoice::find($id);

        $invoiceDetails = FmFeesInvoiceChield::where('fees_invoice_id',$invoiceInfo->id)
                        ->where('school_id', Auth::user()->school_id)
                        ->where('academic_id', getAcademicId())
                        ->get();
        $banks = SmBankAccount::where('active_status', '=', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();

        if($state == 'view'){
            return view('fees::feesInvoice.feesInvoiceView',compact('generalSetting','invoiceInfo','invoiceDetails','banks'));
        }else{
            return view('fees::feesInvoice.feesInvoicePrint',compact('invoiceInfo','invoiceDetails','banks'));
        }
    }

    public function feesInvoiceDelete(Request $request)
    {
        try {
            $invoiceDelete = FmFeesInvoice::find($request->id)->delete();
            if ($invoiceDelete) {
                FmFeesInvoiceChield::where('fees_invoice_id', $request->id)->delete();
            }
            Toastr::success('Delete Successful', 'Success');
            return redirect()->route('fees.fees-invoice-list');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function addFeesPayment($id)
    {
        try {
            $classes = SmClass::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesGroups = FmFeesGroup::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $feesTypes = FmFeesType::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();


            $paymentMethods = SmPaymentMethhod::whereIn('method', ["Cash", "Cheque", "Bank"])
                ->where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $bankAccounts = SmBankAccount::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $invoiceInfo = FmFeesInvoice::find($id);
            $invoiceDetails = FmFeesInvoiceChield::where('fees_invoice_id', $invoiceInfo->id)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $stripe_info = SmPaymentGatewaySetting::where('gateway_name', 'stripe')
                ->where('school_id', Auth::user()->school_id)
                ->first();

            return view('fees::addFessPayment', compact('classes', 'feesGroups', 'feesTypes', 'paymentMethods', 'bankAccounts', 'invoiceInfo', 'invoiceDetails', 'stripe_info'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function feesPaymentStore(Request $request)
    {
        if ($request->total_paid_amount == null) {
            Toastr::warning('Paid Amount Can Not Be Blank', 'Failed');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'bank' => 'required_if:payment_method,Bank',
            'file' => 'mimes:jpg,jpeg,png,pdf',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $destination = 'public/uploads/student/document/';
            $file = fileUpload($request->file('file'), $destination);

            $student = SmStudent::with('parents')->find($request->student_id);

            if ($request->add_wallet > 0) {
                $user = User::find($student->user_id);
                $walletBalance = $user->wallet_balance;
                $user->wallet_balance = $walletBalance + $request->add_wallet;
                $user->update();

                $addPayment = new WalletTransaction();
                $addPayment->amount = $request->add_wallet;
                $addPayment->payment_method = $request->payment_method;
                $addPayment->user_id = $user->id;
                $addPayment->type = 'diposit';
                $addPayment->status = 'approve';
                $addPayment->note = 'Fees Extra Payment Add';
                $addPayment->school_id = Auth::user()->school_id;
                $addPayment->academic_id = getAcademicId();
                $addPayment->save();

                $school = SmSchool::find($user->school_id);
                
                $compact['user_email'] = $user->email;
                $compact['full_name'] = $user->full_name;
                $compact['method'] = $request->payment_method;
                $compact['create_date'] = date('Y-m-d');
                $compact['school_name'] = $school->school_name;
                $compact['current_balance'] = $user->wallet_balance;
                $compact['add_balance'] = $request->total_paid_amount;
                $compact['previous_balance'] = $user->wallet_balance - $request->add_wallet;

                @send_mail($user->email, $user->full_name, "fees_extra_amount_add", $compact);

                //Notification
                sendNotification("Fees Xtra Amount Add", null, $student->user_id, 2);
            }

            $storeTransaction = new FmFeesTransaction();
            $storeTransaction->fees_invoice_id = $request->invoice_id;
            $storeTransaction->payment_note = $request->payment_note;
            $storeTransaction->payment_method = $request->payment_method;
            $storeTransaction->bank_id = $request->bank;
            $storeTransaction->student_id = $request->student_id;
            $storeTransaction->user_id = Auth::user()->id;
            $storeTransaction->file = $file;
            $storeTransaction->paid_status = 'approve';
            $storeTransaction->school_id = Auth::user()->school_id;
            $storeTransaction->academic_id = getAcademicId();
            $storeTransaction->save();

            foreach ($request->fees_type as $key => $type) {
                $id = FmFeesInvoiceChield::where('fees_invoice_id', $request->invoice_id)->where('fees_type', $type)->first('id')->id;
                $storeFeesInvoiceChield = FmFeesInvoiceChield::find($id);
                $storeFeesInvoiceChield->weaver = $request->weaver[$key];
                $storeFeesInvoiceChield->due_amount = $request->due[$key];
                $storeFeesInvoiceChield->paid_amount = $storeFeesInvoiceChield->paid_amount + ($request->paid_amount[$key] - $request->extraAmount[$key]);
                $storeFeesInvoiceChield->fine = $storeFeesInvoiceChield->fine + $request->fine[$key];
                $storeFeesInvoiceChield->update();

                $storeWeaver = new FmFeesWeaver();
                $storeWeaver->fees_invoice_id = $request->invoice_id;
                $storeWeaver->fees_type = $type;
                $storeWeaver->student_id = $request->student_id;
                $storeWeaver->weaver = $request->weaver[$key];
                $storeWeaver->note = $request->note[$key];
                $storeWeaver->school_id = Auth::user()->school_id;
                $storeWeaver->academic_id = getAcademicId();
                $storeWeaver->save();

                if ($request->paid_amount[$key] > 0) {
                    $storeTransactionChield = new FmFeesTransactionChield();
                    $storeTransactionChield->fees_transaction_id = $storeTransaction->id;
                    $storeTransactionChield->fees_type = $type;
                    $storeTransactionChield->weaver = $request->weaver[$key];
                    $storeTransactionChield->fine = $request->fine[$key];
                    $storeTransactionChield->paid_amount = $request->paid_amount[$key];
                    $storeTransactionChield->note = $request->note[$key];
                    $storeTransactionChield->school_id = Auth::user()->school_id;
                    $storeTransactionChield->academic_id = getAcademicId();
                    $storeTransactionChield->save();
                }

                // Income
                $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                $income_head = generalSetting();

                $add_income = new SmAddIncome();
                $add_income->name = 'Fees Collect';
                $add_income->date = date('Y-m-d');
                $add_income->amount = $request->paid_amount[$key];
                $add_income->fees_collection_id = $storeTransaction->id;
                $add_income->active_status = 1;
                $add_income->income_head_id = $income_head->income_head_id;
                $add_income->payment_method_id = $payment_method->id;
                $add_income->created_by = Auth()->user()->id;
                $add_income->school_id = Auth::user()->school_id;
                $add_income->academic_id = getAcademicId();
                $add_income->save();

                // Bank
                if ($request->payment_method == "Bank") {
                    $payment_method = SmPaymentMethhod::where('method', $request->payment_method)->first();
                    $bank = SmBankAccount::where('id', $request->bank)
                        ->where('school_id', Auth::user()->school_id)
                        ->first();
                    $after_balance = $bank->current_balance + $request->paid_amount[$key];

                    $bank_statement = new SmBankStatement();
                    $bank_statement->amount = $request->paid_amount[$key];
                    $bank_statement->after_balance = $after_balance;
                    $bank_statement->type = 1;
                    $bank_statement->details = "Fees Payment";
                    $bank_statement->item_sell_id = $storeTransaction->id;
                    $bank_statement->payment_date = date('Y-m-d');
                    $bank_statement->bank_id = $request->bank;
                    $bank_statement->school_id = Auth::user()->school_id;
                    $bank_statement->payment_method = $payment_method->id;
                    $bank_statement->save();

                    $current_balance = SmBankAccount::find($request->bank);
                    $current_balance->current_balance = $after_balance;
                    $current_balance->update();
                }
            }
            //Notification
            sendNotification("Add Fees Payment", null, $student->user_id, 2);
            sendNotification("Add Fees Payment", null, $student->parents->user_id, 3);

            Toastr::success('Save Successful', 'Success');
            return redirect()->route('fees.fees-invoice-list');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function feesViewPayment(Request $request)
    {
        $feesinvoice = FmFeesInvoice::find($request->invoiceId);
        $feesTranscations = FmFeesTransaction::where('fees_invoice_id', $request->invoiceId)
            ->where('paid_status', 'approve')
            ->where('school_id', Auth::user()->school_id)
            ->get();
        return view('fees::feesInvoice.viewPayment', compact('feesinvoice', 'feesTranscations'));
    }

    public function feesInvoiceSettings()
    {
        try {
            $invoiceSettings = FmFeesInvoiceSettings:: where('school_id', Auth::user()->school_id)->first();
            return view('fees::feesInvoiceSettings', compact('invoiceSettings'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function bankPayment()
    {
        $classes = SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();

        $feesPayments = FmFeesTransaction::with('feeStudentInfo', 'transcationDetails', 'transcationDetails.transcationFeesType')
            ->where('paid_status', 0)
            ->whereIn('payment_method', ['Bank', 'Cheque'])
            ->where('school_id', auth()->user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get();


        return view('fees::bankPayment', compact('classes', 'feesPayments'));
    }

    public function ajaxFeesInvoiceSettingsUpdate(Request $request)
    {
        try {
            $updateData = FmFeesInvoiceSettings::find($request->id);
            $updateData->invoice_positions = $request->invoicePositions;
            $updateData->uniq_id_start = $request->uniqIdStart;
            $updateData->prefix = $request->prefix;
            $updateData->class_limit = $request->classLimit;
            $updateData->section_limit = $request->sectionLimit;
            $updateData->admission_limit = $request->admissionLimit;
            $updateData->weaver = $request->weaver;
            $updateData->school_id = Auth::user()->school_id;
            $updateData->update();
            return response()->json(['success']);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function ajaxSelectStudent(Request $request)
    {
        try {
            $allStudents = SmStudent::where('class_id', $request->classId)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();
            return response()->json([$allStudents]);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function ajaxSelectFeesType(Request $request)
    {
        try {
            $type = substr($request->type, 0, 3);
            if ($type == "grp") {
                $groupId = substr($request->type, 3);
                $feesGroups = FmFeesType::where('fees_group_id', $groupId)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->get();
                return view('fees::_allFeesType', compact('feesGroups'));
            } else {
                $typeId = substr($request->type, 3);
                $feesType = FmFeesType::where('id', $typeId)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', getAcademicId())
                    ->first();
                return view('fees::_allFeesType', compact('feesType'));
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function approveBankPayment(Request $request)
    {
        try {
            $transcation = $request->transcation_id;
            $total_paid_amount = $request->total_paid_amount;
            $transcationInfo = FmFeesTransaction::find($transcation);

            $this->addFeesAmount($transcation , $total_paid_amount);

            //Notification
            $student = SmStudent::with('parents')->find($transcationInfo->student_id);
            sendNotification("Approve Bank Payment", null, 1, 1);
            sendNotification("Approve Bank Payment", null, $student->user_id, 2);
            sendNotification("Approve Bank Payment", null, $student->parents->user_id, 3);

            Toastr::success('Save Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchBankPayment(BankFeesPayment $request)
    {
        try {
            $classes = SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $student_ids = SmStudent::where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('school_id', auth()->user()->school_id)
                ->pluck('id')
                ->toArray();

            $feesPayments = FmFeesTransaction::query();

            if ($request->approve_status != '') {
                $feesPayments->where('paid_status', $request->approve_status);
            }

            if ($request->payment_date) {
                $payment_date = date('Y-m-d', strtotime($request->payment_date));
                $feesPayments->whereDate('created_at', $payment_date);
            }


            $feesPayments = $feesPayments
                ->whereIn('student_id', $student_ids)
                ->whereIn('payment_method', ['Bank', 'Cheque'])
                ->get();


            return view('fees::bankPayment', compact('classes', 'feesPayments'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function rejectBankPayment(Request $request)
    {
        try {
            $transcation = FmFeesTransaction::where('id', $request->transcation_id)->first();
            $fees_transcation = FmFeesTransaction::find($transcation->id);
            $fees_transcation->paid_status = 'reject';
            $fees_transcation->update();

            //Notification
            $student = SmStudent::with('parents')->find($transcation->student_id);
            sendNotification("Approve Bank Payment", null, 1, 1);
            sendNotification("Approve Bank Payment", null, $student->user_id, 2);
            sendNotification("Approve Bank Payment", null, $student->parents->user_id, 3);

            Toastr::success('Save Successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function deleteSingleFeesTranscation($id)
    {
        try {
            $total_amount = 0;
            $transcation = FmFeesTransaction::find($id);
            $allTranscations = FmFeesTransactionChield::where('fees_transaction_id', $transcation->id)->get();
            foreach ($allTranscations as $key => $allTranscation) {
                $total_amount += $allTranscation->paid_amount;

                $transcationId = FmFeesTransaction::find($allTranscation->fees_transaction_id);

                $fesInvoiceId = FmFeesInvoiceChield::where('fees_invoice_id', $transcationId->fees_invoice_id)
                    ->where('fees_type', $allTranscation->fees_type)
                    ->first();

                $storeFeesInvoiceChield = FmFeesInvoiceChield::find($fesInvoiceId->id);
                $storeFeesInvoiceChield->due_amount = $storeFeesInvoiceChield->due_amount + $allTranscation->paid_amount;
                $storeFeesInvoiceChield->paid_amount = $storeFeesInvoiceChield->paid_amount - $allTranscation->paid_amount;
                $storeFeesInvoiceChield->update();
            }

            if ($transcation->payment_method == "Wallet") {
                $user = User::find($transcation->user_id);
                $user->wallet_balance = $user->wallet_balance + $total_amount;
                $user->update();

                $addPayment = new WalletTransaction();
                $addPayment->amount = $total_amount;
                $addPayment->payment_method = $transcation->payment_method;
                $addPayment->user_id = $user->id;
                $addPayment->type = 'fees_refund';
                $addPayment->status = 'approve';
                $addPayment->note = 'Fees Payment';
                $addPayment->school_id = Auth::user()->school_id;
                $addPayment->academic_id = getAcademicId();
                $addPayment->save();
            }

            SmAddIncome::where('fees_collection_id', $id)->delete();
            $transcation->delete();

            //Notification
            $student = SmStudent::with('parents')->find($transcation->student_id);
            sendNotification("Delete Fees Payment", null, 1, 1);
            sendNotification("Delete Fees Payment", null, $student->user_id, 2);
            sendNotification("Delete Fees Payment", null, $student->parents->user_id, 3);

            Toastr::success('Delete Successful', 'Success');
            return redirect()->route('fees.fees-invoice-list');

        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function dueFees()
    {
        try {
            $classes = SmClass::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('fees::feesDue', compact('classes'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function searchDueFees(Request $request)
    {
        try {
            $classes = SmClass::where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            $rangeArr = $request->date_range ? explode('-', $request->date_range) : [date('m/d/Y'), date('m/d/Y')];

            if ($request->date_range) {
                $date_from = date(trim($rangeArr[0]));
                $date_to = date(trim($rangeArr[1]));
            }


            $fees_dues = FmFeesInvoice::when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })
                ->when($request->section, function ($query) use ($request) {
                    $query->whereHas('studentInfo', function($q) use($request){
                        return $q->where('section_id', $request->section);
                    });
                })
                ->whereBetween('due_date', [$date_from, $date_to])
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();

            return view('fees::feesDue', compact('classes', 'fees_dues'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function singlePaymentView($id){
        $generalSetting = SmGeneralSettings::where('school_id', Auth::user()->school_id)->first();

        $transcationInfo = FmFeesTransaction::find($id);

        $transcationDetails = FmFeesTransactionChield::where('fees_transaction_id',$transcationInfo->id)
                            ->where('school_id', Auth::user()->school_id)
                            ->where('academic_id', getAcademicId())
                            ->get();

        $invoiceInfo = FmFeesInvoice::find($transcationInfo->fees_invoice_id);

        return view('fees::feesInvoice.feesInvoiceSingleView',compact('generalSetting','invoiceInfo','transcationDetails'));
        
    }

    public function addFeesAmount($transcation_id , $total_paid_amount){
        $transcation = FmFeesTransaction::find($transcation_id);
        $allTranscations = FmFeesTransactionChield::where('fees_transaction_id', $transcation->id)->get();
        foreach ($allTranscations as $key => $allTranscation) {
            $transcationId = FmFeesTransaction::find($allTranscation->fees_transaction_id);

            $fesInvoiceId = FmFeesInvoiceChield::where('fees_invoice_id', $transcationId->fees_invoice_id)
                ->where('fees_type', $allTranscation->fees_type)
                ->first();

            $storeFeesInvoiceChield = FmFeesInvoiceChield::find($fesInvoiceId->id);
            $storeFeesInvoiceChield->due_amount = $storeFeesInvoiceChield->due_amount - $allTranscation->paid_amount;
            $storeFeesInvoiceChield->paid_amount = $storeFeesInvoiceChield->paid_amount + $allTranscation->paid_amount;
            $storeFeesInvoiceChield->update();

            // Income
            $payment_method = SmPaymentMethhod::where('method', $transcation->payment_method)->first();
            $income_head = generalSetting();

            $add_income = new SmAddIncome();
            $add_income->name = 'Fees Collect';
            $add_income->date = date('Y-m-d');
            $add_income->amount = $allTranscation->paid_amount;
            $add_income->fees_collection_id = $transcation->fees_invoice_id;
            $add_income->active_status = 1;
            $add_income->income_head_id = $income_head->income_head_id;
            $add_income->payment_method_id = $payment_method->id;
            if ($payment_method->id == 3) {
                $add_income->account_id = $transcation->bank_id;
            }
            $add_income->created_by = Auth()->user()->id;
            $add_income->school_id = Auth::user()->school_id;
            $add_income->academic_id = getAcademicId();
            $add_income->save();

            if ($transcation->payment_method == "Bank") {
                $bank = SmBankAccount::where('id', $transcation->bank_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first();

                $after_balance = $bank->current_balance + $total_paid_amount;

                $bank_statement = new SmBankStatement();
                $bank_statement->amount = $allTranscation->paid_amount;
                $bank_statement->after_balance = $after_balance;
                $bank_statement->type = 1;
                $bank_statement->details = "Fees Payment";
                $bank_statement->payment_date = date('Y-m-d');
                $bank_statement->item_sell_id = $transcation->id;
                $bank_statement->bank_id = $transcation->bank_id;
                $bank_statement->school_id = Auth::user()->school_id;
                $bank_statement->payment_method = $payment_method->id;
                $bank_statement->save();

                $current_balance = SmBankAccount::find($transcation->bank_id);
                $current_balance->current_balance = $after_balance;
                $current_balance->update();
            }
            $fees_transcation = FmFeesTransaction::find($transcation->id);
            $fees_transcation->paid_status = 'approve';
            $fees_transcation->update();
        }

        if ($transcation->add_wallet_money > 0) {
            $user = User::find($transcation->user_id);
            $walletBalance = $user->wallet_balance;
            $user->wallet_balance = $walletBalance + $transcation->add_wallet_money;
            $user->update();
    
            $addPayment = new WalletTransaction();
            $addPayment->amount = $transcation->add_wallet_money;
            $addPayment->payment_method = $transcation->payment_method;
            $addPayment->user_id = $user->id;
            $addPayment->type = 'diposit';
            $addPayment->status = 'approve';
            $addPayment->note = 'Fees Extra Payment Add';
            $addPayment->school_id = Auth::user()->school_id;
            $addPayment->academic_id = getAcademicId();
            $addPayment->save();
    
            $school = SmSchool::find($user->school_id);
            $compact['full_name'] = $user->full_name;
            $compact['method'] = $transcation->payment_method;
            $compact['create_date'] = date('Y-m-d');
            $compact['school_name'] = $school->school_name;
            $compact['current_balance'] = $user->wallet_balance;
            $compact['add_balance'] = $transcation->add_wallet_money;
            $compact['previous_balance'] = $user->wallet_balance - $transcation->add_wallet_money;
    
            @send_mail($user->email, $user->full_name, "fees_extra_amount_add", $compact);
    
            sendNotification($user->id, null, null, $user->role_id, "Fees Xtra Amount Add");
        }
    }
}
