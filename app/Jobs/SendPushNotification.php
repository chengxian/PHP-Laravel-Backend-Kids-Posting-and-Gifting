<?php

namespace App\Jobs;

use App\User;
use App\Device;
use App\Notification;

use PushNotification;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPushNotification extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user_id, $type, $text;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $type, $text)
    {
        $this->user_id = $user_id;
        $this->type = $type;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_devices = Device::where('user_id', $this->user_id)->get();
        foreach ($user_devices as $device) {
            $badge = $device->badge + 1;
            $device->badge = $badge;
            $device->save();

            $message = PushNotification::Message($this->message, ['badge'=>$badge, 'custom'=>['type'=>$this->type]]);        
            PushNotification::app('appNameIOS')->to($device->device_token)->send($message);
        }
    }
}
