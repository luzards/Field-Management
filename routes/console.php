<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-schedule-reminders')->everyMinute();
