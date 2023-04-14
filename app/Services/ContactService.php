<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

class ContactService
{
    public function index($userId): Collection
    {
        return Contact::where('user_id', $userId)->get();
    }

    public function show($userId, $contactId)
    {
        return Contact::where([
            ['id', $contactId],
            ['user_id', $userId]
        ])->firstOrFail();
    }

    public function store($userId, $data)
    {
        $numberService = new NumberService();

        $emailService = new EmailService();

        $contact = Contact::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate']
        ]);

        if(isset($data['numbers'])) {
            foreach ($data['numbers'] as $number) {
                $numberService->store(
                   auth()->id(),
                   $contact->id,
                   $number
               );
            }
        }


        if(isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $emailService->store(
                    auth()->id(),
                    $contact->id,
                    $email
                );
            }
        }

        return $contact;
    }

    public function update($userId, $contactId, $data)
    {
        $numberService = new NumberService();

        $emailService = new EmailService();

        $contact = Contact::findOrFail($contactId);

        if($contact->user_id == $userId) {
            $contact->update([
                'name' => $data['name'],
                'surname' => $data['surname'],
                'patronymic' => $data['patronymic'],
                'birthdate' => $data['birthdate']
            ]);


            if (isset($data['numbers'])) {
                foreach ($data['numbers'] as $number) {
                    $numberId = $number['id'];
                    $number = $number['number'];

                    if (empty($number)) {
                        $numberService->destroy(
                            $numberId,
                            $userId,
                            $contactId
                        );
                    } else {
                        $numberService->update(
                            $numberId,
                            $userId,
                            $contactId,
                            $number
                        );
                    }
                }
            }

            if (isset($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    $emailId = $email['id'];
                    $email = $email['email'];

                    if (empty($email)) {
                        $emailService->destroy(
                            $emailId,
                            $userId,
                            $contactId
                        );
                    } else {
                        $emailService->update(
                            $emailId,
                            $userId,
                            $contactId,
                            $email
                        );
                    }
                }
            }
        }

        return $contact;
    }

    public function destroy($userId, $contactId): bool
    {
        $numberService = new NumberService();

        $emailService = new EmailService();

        $contact = Contact::findOrFail($contactId);

        if($contact->user_id == $userId) {
            $numberService->destroyByContactId($contactId);

            $emailService->destroyByContactId($contactId);

            $contact->delete();

            return true;
        }

        return false;
    }
}
