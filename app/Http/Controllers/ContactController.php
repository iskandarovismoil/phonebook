<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Interfaces\ContactServiceInterface;
use App\Mail\BirthDayAlert;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use App\Services\ContactService;
use App\Utilities\Data;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;

        $this->middleware('jwt.verify');
    }

    public function index(): AnonymousResourceCollection
    {
        $contacts = $this->contactService->index(
            auth()->id()
        );

        return ContactResource::collection($contacts);
    }

    public function show($contactId): ContactResource
    {
        $contact = $this->contactService->show(
            auth()->id(),
            $contactId
        );

        return ContactResource::make($contact);
    }

    public function store(ContactStoreRequest $request): ContactResource
    {
        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

        $contact = $this->contactService->store(
            auth()->id(),
            $data
        );

        return ContactResource::make($contact);
    }

    public function update(ContactUpdateRequest $request, $contactId)
    {
        $contact = Contact::where([
            ['id', $contactId],
            ['user_id', auth()->id()]
        ]);

        $data = $request->only([
            "name",
            "surname",
            "patronymic",
            "birthdate",
            "numbers",
            "emails",
        ]);

//        $contact = $this->contactService->update(
//            auth()->id(),
//            $contactId,
//            $data
//        );

        $contact->update([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'patronymic' => $data['patronymic'],
            'birthdate' => $data['birthdate'],
        ]);

        return print_r($contact->get());

        //return ContactResource::make($contact);
    }

    public function destroy(Contact $contact): ContactResource
    {
        $contact = $this->contactService->destroy($contact);

        return ContactResource::make($contact);
    }
}
