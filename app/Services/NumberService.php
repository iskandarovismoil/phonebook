<?php

namespace App\Services;

use App\Http\Requests\ContactStoreRequest;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class NumberService
{
    public function store($userId, $contactId, $number)
    {
        return Number::create([
            'user_id' => $userId,
            'contact_id' => $contactId,
            'number' => $number
        ]);
    }

    public function update($numberId, $userId, $contactId, $number)
    {
        return Number::where([
            'id' => $numberId,
            'user_id' => $userId,
            'contact_id' => $contactId
        ])->update([
            'number' => $number
        ]);
    }

    public function destroy($numberId, $userId, $contactId): void
    {
        Number::where([
            'id' => $numberId,
            'user_id' => $userId,
            'contact_id' => $contactId,
        ])->delete();
    }

    public function destroyByUserId($userId): void
    {
        Number::where([
            'user_id' => $userId,
        ])->delete();
    }

    public function destroyByContactId($contactId): void
    {
        Number::where([
            'contact_id' => $contactId,
        ])->delete();
    }
}
