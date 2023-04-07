<?php

namespace App\Console\Commands;

use App\Mail\BirthDayAlert;
use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AutoBirthDayAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:birthdayalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $contacts = Contact::whereMonth('birthdate', date('m'))
            ->whereDay('birthdate', date('d'))
            ->get();

        if ($contacts->count() > 0) {
            foreach ($contacts as $contact) {
                Mail::to($contact->user)->send(new BirthDayAlert($contact));
            }
        }

        return 0;
    }
}
