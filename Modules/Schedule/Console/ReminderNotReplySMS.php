<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 1/19/18
 * Time: 8:34 AM
 */
namespace Modules\Schedule\Console;


use Illuminate\Console\Command;
use Modules\Schedule\Services\ReliefReminderNotResponse;

class ReminderNotReplySMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders teacher not reply using SMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $reminder = new ReliefReminderNotResponse();
        $reminder->sendReminders();

        $this->info('Done');
    }
}