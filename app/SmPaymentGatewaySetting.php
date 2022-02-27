<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmPaymentGatewaySetting extends Model
{
    public static function getStripeDetails(){
    	
		try {
			$stripeDetails = SmPaymentGatewaySetting::select('*')->where('gateway_name', '=', 'Stripe')->first();
				if(!empty($stripeDetails)){
					return $stripeDetails->stripe_publisher_key;
				}
		} catch (\Exception $e) {
			$data=[];
			return $data;
		}
    }
}
