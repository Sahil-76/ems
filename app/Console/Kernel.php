<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\ems\ElectricityController;
use App\Http\Controllers\ems\NotificationController;
use App\Http\Controllers\ems\LiveAttendanceController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    //    \App\Console\Commands\NotifyAbsent::class,
   ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('notify:absent')->dailyAt('14:30')
        ->skip(function(){
            return now()->isSunday();
        })->timezone('Asia/Kolkata');
        
        $schedule->command('leave:change-forwarded-status')->dailyAt('09:30')
        ->skip(function(){
            return now()->isSunday();
        })->timezone('Asia/Kolkata');
        
        $schedule->call(function(){
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL, "https://ems.tka-in.com/remove/document");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_exec ($curl);
            curl_close ($curl);
        }) ->weekly()->sundays()
            ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $attendance= new LiveAttendanceController();
            $attendance->storeAttendance();
        })->everyFiveMinutes()
        ->timezone('Asia/Kolkata');


        $schedule->call(function(){
            $electricity= new ElectricityController();
            $electricity->emailNotifier("start_unit");
        })->daily()->at('10:00')
        ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $electricity= new ElectricityController();
            $electricity->emailNotifier("end_unit");
        })->daily()->at('17:00')
        ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $attendance= new LiveAttendanceController();
            $attendance->removeAttendance();
        })->daily()->at('9:00')
        ->timezone('Asia/Kolkata');

        $schedule->command('add:balance')->monthlyOn('01','00:00');

        $schedule->command('notify:incomplete-profile')->weekly()->mondays()->at('9:30')->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $notification = new NotificationController();
            $notification->deleteNotifications();
        })->daily()->at('9:30')
        ->timezone('Asia/Kolkata');

        $schedule->call(function(){
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL, "https://ems.tka-in.com/reset/birthday/readon");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_exec ($curl);
            curl_close ($curl);
        })->daily()->at('6:00')->timezone('Asia/Kolkata');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
