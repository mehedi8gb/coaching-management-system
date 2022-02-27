@extends('backEnd.master')
    @section('title') 
        @lang('wallet::wallet.my_wallet')
    @endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('wallet::wallet.wallet_details')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('wallet::wallet.my_wallet')</a>
                <a href="#">@lang('wallet::wallet.wallet_details')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor mt-20">
    <div class="container-fluid p-0">
        @include('wallet::_addWallet')
    </div>
</div>
@endsection
@push('script')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    var paymentValue= '';
    $("#addWalletPaymentMethod").on("change", function() {
        paymentValue = $(this).val();
    });
$(function() {
        var $form = $("form#addWalletAmount");
        var publisherKey = '{!!$stripe_info->gateway_publisher_key !!}';
        var ccFalse= false;
        $('form#addWalletAmount').on('submit', function(e) {
            e.preventDefault();
            if(paymentValue == "Stripe"){
                if (!ccFalse){
                    e.preventDefault();
                    Stripe.setPublishableKey(publisherKey);
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
            }
        });

        function stripeResponseHandler(status, response) {
            if (response.error) {
                $('.error')
                    .removeClass('hide')
                    .find('.alert')
                    .text(response.error.message);
            } else {
                // token contains id, last4, and card type
                var token = response['id'];
                // insert the token into the form so it gets submitted to the server
                $form.find('input[type=text]').empty();

                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
            }
        }
});
</script>
@endpush