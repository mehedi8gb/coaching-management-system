<?php
$setting = App\SmGeneralSettings::find(1);
if(isset($setting->copyright_text)){ $copyright_text = $setting->copyright_text; }else{ $copyright_text = 'Copyright © 2019 All rights reserved | This template is made with by Codethemes'; }
if(isset($setting->logo)) { $logo = $setting->logo; } else{ $logo = 'public/uploads/settings/logo.png'; }

if(isset($setting->favicon)) { $favicon = $setting->favicon; } else{ $favicon = 'public/backEnd/img/favicon.png'; }
 
$login_background = App\SmBackgroundSetting::where([['is_default',1],['title','Login Background']])->first(); 
 
if(empty($login_background)){
    $css = "background: url(".url('public/backEnd/img/login-bg.png').")  no-repeat center; background-size: cover; ";
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('public/backEnd/login')}}/css/4_3_1_bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('public/backEnd/login')}}/css/themify-icons.css">
   
    <link rel="stylesheet" href="{{asset('public/backEnd/login')}}/css/style.css">
     
    <style>
        .white.get-login-access {
            background-color: #fff !important;
            border-radius: 5px !important;
            }
    </style>
  </head>
  <body>
    
    <!-- code start here -->

    <div class="login_resister_area login_bg_1">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 offset-xl-2">
                    
                        
                   
                    <div class="importtant_button d-block justify-content-center">
                        <ul>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',1)->first();
                                    $email = $user->email;
                                    dd($user );

                                    ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">
                                    <button type="submit" class="white get-login-access">Super Admin</button>
                                </form> 
                            </li>
                            <li>
                            
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',5)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Admin</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',4)->first();
                                    $email = $user->email; ?>

                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Teacher</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',6)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">

                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Accountant</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',7)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Receptionist</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',8)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Librarian</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',2)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Student</button>
                                </form> 
                            </li>
                            <li>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <?php 
                                    echo csrf_field();
                                    $user =  DB::table('users')->select('email')->where('role_id',3)->first();
                                    $email = $user->email; ?>
                                    <input type="hidden" name="email" value="{{$email}}">
                                    <input type="hidden" name="password" value="123456">

                                    <button type="submit" class="white get-login-access">Parents</button>
                                </form> 
                            </li>
                        </ul>
                    </div>
                   
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-6 col-md-8">
                            <div class="main_login_form">
                                <div class="login_header text-center">
                                    <div class="logo_img"> 
                                        <a href="{{url('/login')}}"> 
                                            <img src="{{asset($setting->logo)}}" alt="">
                                        </a>
                                    </div>
                                    <h5>Login Details</h5>
                        <?php if(session()->has('message-success') != ""): ?>
                            <?php if(session()->has('message-success')): ?>
                            <p class="text-success"><?php echo e(session()->get('message-success')); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(session()->has('message-danger') != ""): ?>
                            <?php if(session()->has('message-danger')): ?>
                            <p class="text-danger"><?php echo e(session()->get('message-danger')); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                                </div>
                                <form method="POST" class="loginForm" action="<?php echo e(route('login')); ?>">
                                    <input type="hidden" id="url" value="{{url('/')}}">
                                    <?php  echo csrf_field(); ?>
                                    <div class="single_input">
                                        <input type="text" placeholder="Enter Email address" name="email">
                                        <span class="addon_icon" >
                                            <i class="ti-email"></i>
                                        </span>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback text-left pl-3" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                                    </div>
                                    <div class="single_input">
                                        <input type="password" placeholder="Enter Password" name="password">
                                        <span class="addon_icon" >
                                            <i class="ti-key"></i>
                                        </span>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback text-left pl-3" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="checkbox">
                                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                            <label for="rememberMe">Remember Me</label>
                                        </div>
                                        <div class="forgot_pass" >
                                            <a href="<?php echo e(url('recovery/passord')); ?>">Forget Password?</a>
                                        </div>
                                    </div>
                                    <div class="login_button text-center">
                                        <button type="submit" class="primary-btn fix-gr-bg">
                                            <span class="ti-lock mr-2"></span>
                                            Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="footer_text text-center">
                    <p>{!! $copyright_text !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!--/ code start here -->







    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('public/backEnd/login')}}/js/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="{{asset('public/backEnd/login')}}/js/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="{{asset('public/backEnd/login')}}/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    
     
  </body>
</html>
