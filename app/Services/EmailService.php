<?php

namespace App\Services;

use App\Http\Requests\ContactStoreRequest;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmailService
{
    public function store($userId, $contactId, $email)
    {
        return Email::create([
            'user_id' => $userId,
            'contact_id' => $contactId,
            'email' => $email
        ]);
    }

    public function update($emailId, $userId, $contactId, $email)
    {
        return Email::where([
            'id' => $emailId,
            'user_id' => $userId,
            'contact_id' => $contactId
        ])->update([
            'email' => $email
        ]);
    }

    public function destroy($emailId, $userId, $contactId): void
    {
        Email::where([
            'id' => $emailId,
            'user_id' => $userId,
            'contact_id' => $contactId,
        ])->delete();
    }

    public function destroyByUserId($userId): void
    {
        Email::where([
            'user_id' => $userId,
        ])->delete();
    }

    public function destroyByContactId($contactId): void
    {
        Email::where([
            'contact_id' => $contactId,
        ])->delete();
    }
}
