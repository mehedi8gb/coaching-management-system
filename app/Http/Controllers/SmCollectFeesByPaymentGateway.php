<?php

namespace App\Http\Controllers;

use Stripe;
use App\YearCheck;  
use PayPal\Api\Item;
use PayPal\Api\Payer;
use App\SmFeesPayment;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use App\SmGeneralSettings;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use App\SmFeesAssignDiscount;
use App\SmPaymentGatewaySetting;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\URL;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class SmCollectFeesByPaymentGateway extends Controller
{

    private $_api_context;
    private $mode;
    private $client_id;
    private $secret;

    public function __construct()
    {

        try {
            /** PayPal api context **/
            $paypalDetails = SmPaymentGatewaySetting::select('paypal_username', 'paypal_password', 'paypal_signature', 'paypal_client_id', 'paypal_secret_id')
            ->where('gateway_name', '=', 'Paypal')->where('school_id',Auth::user()->school_id)->first();

            $paypal_conf = Config::get('paypal');
            $this->_api_context = new ApiContext(
                new OAuthTokenCredential(
                    $paypalDetails->paypal_client_id,
                    $paypalDetails->paypal_secret_id
                )
            );
            $this->_api_context->setConfig($paypal_conf['settings']);
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function collectFeesByGateway($amount, $student_id, $type)
    {

        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();

            $applied_discount = [];
            foreach ($discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            return view('backEnd.feesCollection.collectFeesByGateway', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payByPaypal(Request $request)
    {
        try {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item_1 = new Item();

            $item_1->setName('Item 1')
                /** item name **/
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice($request->get('amount'));
            /** unit price **/

            $item_list = new ItemList();
            $item_list->setItems(array($item_1));

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal($request->get('amount'));

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Your transaction description');

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(URL::to('paypal-return-status'))
                /** Specify return URL **/
                ->setCancelUrl(URL::to('paypal-return-status'));

            $user = Auth::user();
            $fees_payment = new SmFeesPayment();
            $fees_payment->student_id = $request->student_id;
            $fees_payment->fees_type_id = $request->fees_type_id;
            //  $fees_payment->fees_discount_id = $request->discount_group;
            // $fees_payment->discount_amount = !empty($request->discount_amount)? $request->discount_amount: 0;
            //  $fees_payment->fine = !empty($request->fine)? $request->fine: 0;
            $fees_payment->amount = $request->amount;
            $fees_payment->payment_date = date('Y-m-d');
            $fees_payment->payment_mode = 'P';
            $fees_payment->created_by = $user->id;
            $fees_payment->school_id = Auth::user()->school_id;
            $fees_payment->save();

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));
            /** dd($payment->create($this->_api_context));exit; **/
            try {
                $payment->create($this->_api_context);
            } catch (\PayPal\Exception\PPConnectionException $ex) {

                if (Config::get('app.debug')) {
                    Session::put('error', 'Connection timeout');
                    return Redirect::to('/');
                } else {
                    Session::put('error', 'Some error occur, sorry for inconvenient');
                    return Redirect::to('/');
                }
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }

            /** add payment ID to session **/
            Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return Redirect::away($redirect_url);
            }
            Session::put('error', 'Unknown error occurred');
            return Redirect::to('/');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getPaymentStatus(Request $request)
    {


        try {
            /** Get the payment ID before session clear **/
            $payment_id = Session::get('paypal_payment_id');

            /** clear the session payment ID **/
            Session::forget('paypal_payment_id');
            if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
                Session::put('error', 'Payment failed');
                return Redirect::to('/');
            }

            $payment = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId(Input::get('PayerID'));

            /**Execute the payment **/
            $result = $payment->execute($execution, $this->_api_context);

            if ($result->getState() == 'approved') {

                Session::put('success', 'Payment success');
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
                // return redirect('student-fees')->with('message-success', 'You made this payment Successfully.');
            }
            Session::put('error', 'Payment failed');
            return Redirect::to('/');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function collectFeesStripe($amount, $student_id, $type)
    {


        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();

            $applied_discount = [];
            foreach ($discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            return view('backEnd.feesCollection.collectFeesStripeView', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function stripeStore(Request $request)
    {
        try {
            $system_currency = '';
            $currency_details = SmGeneralSettings::select('currency')->where('id', 1)->first();
            if (isset($currency_details)) {
                $system_currency = $currency_details->currency;
            }
            $stripeDetails = SmPaymentGatewaySetting::select('stripe_api_secret_key', 'stripe_publisher_key')->where('gateway_name', '=', 'Stripe')->first();

            Stripe\Stripe::setApiKey($stripeDetails->stripe_api_secret_key);
            $charge =  Stripe\Charge::create([
                "amount" => $request->real_amount * 100,
                "currency" => $system_currency,
                "source" => $request->stripeToken,
                "description" => "Student Fees payment"
            ]);
            if ($charge) {
                $user = Auth::user();
                $fees_payment = new SmFeesPayment();
                $fees_payment->student_id = $request->student_id;
                $fees_payment->fees_type_id = $request->fees_type_id;
                //  $fees_payment->fees_discount_id = $request->discount_group;
                // $fees_payment->discount_amount = !empty($request->discount_amount)? $request->discount_amount: 0;
                //  $fees_payment->fine = !empty($request->fine)? $request->fine: 0;
                $fees_payment->amount = $request->real_amount;
                $fees_payment->payment_date = date('Y-m-d');
                $fees_payment->payment_mode = 'P';
                $fees_payment->created_by = $user->id;
                $fees_payment->school_id = Auth::user()->school_id;
                $fees_payment->save();

                Toastr::success('Operation successful', 'Success');
                return redirect('student-fees');
                // return redirect('student-fees')->with('message-success', 'You made this payment Successfully with your Card.');
            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('student-fees');
                // return redirect('student-fees')->with('message-danger', 'Something Goes Wrong');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
