<?php

namespace App\Repositories;

use App\Http\Requests\ContactStoreRequest;
use App\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function getAll(): Collection
    {
        return Contact::where('user_id', auth()->id())->get();
    }

    public function getById($contactId)
    {
        return Contact::where([
            ['id', $contactId],
            ['user_id', auth()->id()]
        ])->firstOrFail();
    }

    public function create(ContactStoreRequest $request)
    {
        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

        $contact = Contact::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate']
        ]);

        if(isset($data['numbers'])) {
            foreach ($data['numbers'] as $number) {
                $contact->numbers()->create([
                    'number' => $number
                ]);
            }
        }

        if(isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $contact->emails()->create([
                    'email' => $email
                ]);
            }
        }

        return $contact;
    }

    public function update(ContactStoreRequest $request, $contactId)
    {
        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

        $contact = Contact::where([
            ['id', $contactId],
            ['user_id', auth()->id()]
        ])->update([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate']
        ]);

        if(isset($data['numbers'])) {
            foreach ($data['numbers'] as $number) {
                $numberId = $number['id'];
                $number = $number['number'];

                if(empty($number)) {
                    $contact->whereHas('numbers', function (Builder $query) use ($numberId) {
                        $query->where('id', '=', $numberId);
                    })->delete();
                } else {
                    $contact->whereHas('numbers', function (Builder $query) use ($numberId) {
                        $query->where('id', '=', $numberId);
                    })->update([
                        'number' => $number['number']
                    ]);
                }
            }
        }

        if(isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $emailId = $email['id'];
                $email = $email['email'];

                if(empty($email)) {
                    $contact->whereHas('emails', function (Builder $query) use ($emailId) {
                        $query->where('id', '=', $emailId);
                    })->delete();
                } else {
                    $contact->whereHas('emails', function (Builder $query) use ($emailId) {
                        $query->where('id', '=', $emailId);
                    })->update([
                        'email' => $email['email']
                    ]);
                }
            }
        }

        return $contact;
    }

    public function delete(Contact $contact)
    {
        $contact->transaction(function() {
            $this->numbers()->delete();
            $this->emails()->delete();
            parent::delete();
        });
    }
}
