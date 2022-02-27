<?php

namespace App\Http\Middleware;

use App\SmCustomLink;
use App\SmFrontendPersmission;
use App\SmHeaderMenuManager;
use App\SmSocialMediaIcon;
use Closure;
use Illuminate\Support\Facades\Session;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      
        $domain = $request->subdomain;
     
        Session::put('domain', $domain);
        $host = $request->getHttpHost();
        $school = \App\SmSchool::findOrFail(1);

        if (moduleStatusCheck('Saas')) {
            if ($domain) {
                $school = \App\SmSchool::where(['domain' => $domain, 'active_status' => 1])->firstOrFail();
                $request->route()->forgetParameter('subdomain');
            } else if ($host != config('app.short_url') and config('app.allow_custom_domain')) {
                $school = \App\SmSchool::where(['custom_domain' => $host, 'active_status' => 1])->firstOrFail();
                Session::put('domain', $school->domain);
            }
        }

        app()->forgetInstance('school');
        app()->instance('school', $school);

        view()->composer('frontEnd.home.front_master', function ($view) use ($school) {

            $data = [
                'social_permission' => SmFrontendPersmission::where('name', 'Social Icons')->where('parent_id', 1)->where('is_published', 1)->where('school_id', app('school')->id)->first(),
                'menus' => SmHeaderMenuManager::whereNull('parent_id')->where('school_id', app('school')->id)->orderBy('position')->get(),
                'custom_link' => SmCustomLink::where('school_id', app('school')->id)->first(),
                'social_icons' => SmSocialMediaIcon::where('school_id', app('school')->id)->where('status', 1)->get(),
                'school' => $school,
            ];

            $view->with($data);

        });
        
        return $next($request);
    }
}
