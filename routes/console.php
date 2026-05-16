<?php

use Illuminate\Support\Facades\Schedule;

// Runs every minute; the command internally decides which monitors are due
Schedule::command('monitors:check')->everyMinute();