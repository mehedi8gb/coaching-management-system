<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\SmEmailSetting;

use Illuminate\Contracts\Mail\Mailer;

class SendUserMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_info = [];
    protected $sender;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_info, $sender)
    {
        $this->user_info = $user_info;
        $this->sender = $sender;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
       
        foreach($this->user_info as $info){

            $mailer->send('backEnd.studentInformation.user_credential', ['data'=> $info], function ($message) use($info) {

                $message->from($this->sender['system_email'], $this->sender['school_name']);
                
                $message->to($info['email'])->subject('Login Credentials');

            });
        } 
    }
}
