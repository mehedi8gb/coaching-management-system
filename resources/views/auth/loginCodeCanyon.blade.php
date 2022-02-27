<?php
$setting = App\SmGeneralSettings::find(1);
if(isset($setting->copyright_text)){ $copyright_text = $setting->copyright_text; }else{ $copyright_text = 'Copyright © 2019 All rights reserved | This template is made with by Codethemes'; }
if(isset($setting->logo)) { $logo = $setting->logo; } else{ $logo = 'public/uploads/settings/logo.png'; }

if(isset($setting->favicon)) { $favicon = $setting->favicon; } else{ $favicon = 'public/backEnd/img/favicon.png'; }
 
$login_background = App\SmBackgroundSetting::where([['is_default',1],['title','Login Background']])->first(); 
 
if(empty($login_background)){
    $css = "";
}else{
    if(!empty($login_background->image)){
        $css = "background: url('". url($login_background->image) ."')  no-repeat center;  background-size: cover;";
 
    }else{
        $css = "background:".$login_background->color;
    }
}
$active_style = App\SmStyle::where('is_active', 1)->first();

$ttl_rtl = $setting->ttl_rtl;
?>

<!doctype html>
<html lang="en">

<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset($favicon)}}" type="image/png"/>
    <title>{{__('Login')}} | {{@$setting->site_title}}</title>
    <meta name="_token" content="{!! csrf_token() !!}"/> 
    <link rel="stylesheet" href="{{asset('public/backEnd/login2')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('public/backEnd/login2')}}/themify-icons.css">
    <link rel="stylesheet" href="{{asset('public/backEnd/login2')}}/css/style.css">
	<link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/toastr.min.css"/>
    <title>{{isset($setting)? !empty($setting->site_title) ? $setting->site_title : 'System ': 'System '}} | @lang('lang.login')</title>
    <style>
    
.loginButton {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.loginButton{
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}
.singleLoginButton{
    flex: 22% 0 0;
}

.loginButton .get-login-access {
    display: block;
    width: 100%;
    border: 1px solid #fff;
    border-radius: 5px;
    margin-bottom: 20px;
    padding: 5px;
    white-space: nowrap;
}
@media (max-width: 576px) {
  .singleLoginButton{
    flex: 49% 0 0;
  }
}
@media (max-width: 576px) {
  .singleLoginButton{
    flex: 49% 0 0;
  }
  .loginButton .get-login-access {
    margin-bottom: 10px;
}
}
    </style>
</head> 
  <body >
     <div class="in_login_part"  style="{{$css}}">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-8 col-lg-5 col-xl-4 col-md-7">
					@if($errors->any())
						{{ implode('', $errors->all('<div>:message</div>')) }}
					@endif
                    <div class="in_login_content">
                        @if(!empty($setting->logo))<img src="{{asset($setting->logo)}}" alt="Login Panel">@endif
                        <div class="in_login_page_iner">
                            <div class="in_login_page_header">
                                <h5>@lang('lang.login') @lang('lang.details')</h5>
                            </div>
                                <form method="POST" class="loginForm" action="{{url('/login')}}" id="infix_form">
                                    @csrf

                                    <input type="hidden" name="school_id" value="1">
                                    <input type="hidden" name="username" id="username-hidden">
                                    
                                  <?php if(session()->has('message-danger') != ""): ?>
                                      <?php if(session()->has('message-danger')): ?>
                                      <p class="text-danger"><?php echo e(session()->get('message-danger')); ?></p>
                                      <?php endif; ?>
                                  <?php endif; ?>
                                    <input type="hidden" id="url" value="{{url('/')}}"> 
                          
                                <div class="in_single_input">
                                    <input type="text" placeholder="@lang('lang.enter') @lang('lang.email')" name="email" class="{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{old('email')}}" id="email-address">
                                    <span class="addon_icon">
                                        <i class="ti-email"></i>
                                    </span>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback text-left pl-3 d-block" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="in_single_input">
                                    <input type="password" placeholder="@lang('lang.enter')  @lang('lang.password')" name="password" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" value="{{old('password')}}">
                                    <span class="addon_icon">
                                        <i class="ti-key"></i>
                                    </span>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback text-left pl-3 d-block" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="in_checkbox">
                                        <div class="boxes">
                                            <input type="checkbox" id="Remember">
                                            <label for="Remember">@lang('lang.remember_me')</label>
                                        </div>
                                    </div>
                                    <div class="in_forgot_pass">
                                        <a href="{{url('recovery/passord')}}">@lang('lang.forget') @lang('lang.password') ? </a>
                                    </div>
                                </div>
                                <div class="in_login_button text-center">
                                    <button type="submit" class="in_btn" id="btnsubmit">
                                        <span class="ti-lock"></span>
                                        @lang('lang.login')
                                    </button>
                                </div>
                                {{-- <div class="create_account text-center">
                                    <p>Don’t have an account? <a href="#">Create Here</a></p>
                                </div> --}}
                            </form>

                            
                        </div>

		@if(Illuminate\Support\Facades\Config::get('app.app_sync'))
			<div class="row justify-content-center align-items-center" style="margin-top: 25px !important;">
				<div class="col-lg-12 col-md-12 text-center mt-30 btn-group" id="btn-group">

					<div class="loginButton">
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',1)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',1)->first();
			                        $email = $user->email;

			                        ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">
			                        <button type="submit" class="white get-login-access">Super Admin</button>
			                    </form>

						</div>
						@endif

						@php
							  $user =  DB::table('users')->select('email')->where('role_id',5)->first();
						@endphp

						@if (!empty($user))
							
					
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                      
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Admin</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',4)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        // $user =  DB::table('users')->select('email')->where('role_id',4)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">

			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Teacher</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',6)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',6)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">

			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Accountant</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',7)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',7)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Receptionist</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',8)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',8)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Librarian</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',2)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',2)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Student</button>
			                    </form>
						</div>
						@endif
						@php
							  $user =  DB::table('users')->select('email')->where('role_id',3)->first();
						@endphp

						@if (!empty($user))
						<div class="singleLoginButton">
							
								<form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
			                        <?php 
			                        echo csrf_field();
			                        $user =  DB::table('users')->select('email')->where('role_id',3)->first();
			                        $email = $user->email; ?>
			                        <input type="hidden" name="school_id" value="1">
			                        <input type="hidden" name="email" value="{{$email}}">
			                        <input type="hidden" name="password" value="123456">

			                        <button type="submit" class="white get-login-access">Parents</button>
			                    </form>
						</div> 
						@endif
					</div> 
							
				</div>
			</div>
			@endif
			
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{asset('public/backEnd/login2')}}/js/jquery-3.4.1.min.js"></script>
    <script src="{{asset('public/backEnd/login2')}}/js/popper.min.js"></script>
	<script src="{{asset('public/backEnd/login2')}}/js/bootstrap.min.js"></script> 
	<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/toastr.min.js"></script>

 

	<script>
	$(document).ready(function () {
	 
		$('#btnsubmit').on('click',function()
		{
		$(this).html('Please wait ...')
			.attr('disabled','disabled');
		$('#infix_form').submit();
		});
	 
	 });


	$(document).ready(function() {
	        $("#email-address").keyup(function(){ 
			  $("#username-hidden").val($(this).val());
			});
	    });

	 </script>



	{!! Toastr::message() !!}
  </body>
</html>
