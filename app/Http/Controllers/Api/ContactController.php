<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BirthDayAlert;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use App\Utilities\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;

class ContactController extends Controller
{
    private const MESSAGES = [
        'name.required' => 'Поля имени .',
        'surname.required' => 'Поля фамилии',
    ];

    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    function get($id = null): JsonResponse {
        if($id) {
            $contact = Contact::where([
                ['id', $id],
                ['user_id', auth()->id()]
            ]);

            Mail::to($contact->user())->send(new BirthDayAlert($contact));

            return Data::makeResponseForm(
                true,
                $contact,
                200
            );
        } else {
            $contacts = Contact::where('user_id', auth()->id())->with(['numbers', 'emails'])->get();

            return Data::makeResponseForm(
                true,
                $contacts,
                200
            );
        }
    }

    function create(Request $request): JsonResponse {
        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

        $validator = Validator::make($data, [
            'name' => 'required',
            'numbers' => 'array',
            'emails' => 'array',
            'numbers.*' => 'integer',
            'emails.*' => 'email',
        ], self::MESSAGES);

        if($validator->fails()) {
            return Data::makeResponseForm(
                false,
                null,
                400,
                $validator->errors()->toArray()
            );
        }

        $contact = Contact::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate'],
        ]);

        if(isset($data['numbers'])) {
            foreach ($data['numbers'] as $number) {
                $contact->numbers()->create([
                    'contact_id' => $contact->id,
                    'number' => $number
                ]);
            }
        }

        if(isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $contact->emails()->create([
                    'contact_id' => $contact->id,
                    'email' => $email
                ]);
            }
        }

        $contact = Contact::where('id', $contact->id)->with(['numbers', 'emails'])->get();

        return Data::makeResponseForm(
            true,
            $contact,
            200
        );
    }

    function edit(Request $request, Int $id): JsonResponse {
        $contact = Contact::where([
            ['id', $id],
            ['user_id', auth()->id()]
        ]);

        if(!$contact->exists()) {
            return Data::makeResponseForm(
                false,
                null,
                404,
                "Contact not found!"
            );
        }

        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

        $validator = Validator::make($data, [
            'name' => 'required',
            'numbers' => 'array',
            'emails' => 'array',
            'numbers.*.id' => 'integer|exists:numbers',
            'numbers.*.number' => 'nullable|integer',
            'emails.*.id' => 'integer|exists:emails',
            'emails.*.email' => 'nullable|email',
        ], self::MESSAGES);

        if($validator->fails()){
            return Data::makeResponseForm(
                false,
                null,
                400,
                $validator->errors()->toArray()
            );
        }

        $contact->update([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate'],
        ]);

        if(isset($data['numbers'])) {
            foreach ($data['numbers'] as $number) {
                if(empty($number['number'])) {
                    Number::where('id', $number['id'])->delete();
                } else {
                    Number::where('id', $number['id'])->update([
                        'number' => $number['number']
                    ]);
                }
            }
        }

        if(isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                if(empty($email['number'])) {
                    Email::where('id', $email['id'])->delete();
                } else {
                    Email::where('id', $email['id'])->update([
                        'email' => $email['email']
                    ]);
                }
            }
        }

        return Data::makeResponseForm(
            true,
            $contact->with(['numbers', 'emails'])->get(),
            200
        );
    }

    function delete(Int $id): JsonResponse {
        $contact = Contact::where([
            ['id', $id],
            ['user_id', auth()->id()]
        ]);

        if(!$contact->exists()) {
            return Data::makeResponseForm(
                false,
                null,
                404,
                "Contact not found!"
            );
        }

        Number::where('contact_id', $id)->delete();
        Email::where('contact_id', $id)->delete();
        $contact->delete();

        return Data::makeResponseForm(
            true,
            null,
            200
        );
    }

    function search(Request $request): JsonResponse {
        $data = $request->only([
            "keyword",
        ]);

        $keyWord = $data['keyword'];

        $contacts = Contact::where('user_id', '=', auth()->id())
            ->whereHas('numbers', function ($query) use ($keyWord){
                $query->where('number', 'like', '%'.$keyWord.'%');
            })
            ->orWhereHas('emails', function ($query) use ($keyWord){
                $query->where('email', 'like', '%'.$keyWord.'%');
            })
            ->orWhere('name', 'LIKE', "%{$keyWord}%")
            ->orWhere('surname', 'LIKE', "%{$keyWord}%")
            ->orWhere('patronymic', 'LIKE', "%{$keyWord}%");

        return Data::makeResponseForm(
            true,
            $contacts->with(['numbers', 'emails'])->get(),
            200
        );
    }
}
