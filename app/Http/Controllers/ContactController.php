<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Http\Resources\ContactResource;
use App\Interfaces\ContactRepositoryInterface;
use App\Mail\BirthDayAlert;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Number;
use App\Repositories\ContactRepository;
use App\Utilities\Data;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private ContactRepositoryInterface $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;

        $this->middleware('jwt.verify');
    }

    public function index(): AnonymousResourceCollection
    {
        $contacts = $this->contactRepository->getAll();

        return ContactResource::collection($contacts);
    }

    public function show($contactId): ContactResource
    {
        $contact = $this->contactRepository->getById($contactId);

        return ContactResource::make($contact);
    }

    public function store(ContactStoreRequest $request): ContactResource
    {
        $contact = $this->contactRepository->create($request);

        return ContactResource::make($contact);
    }

    public function update(ContactStoreRequest $request, $contactId): ContactResource
    {
        return print_r($contactId);

        $contact = $this->contactRepository->update($request, $contactId);

        return ContactResource::make($contact);
    }

    public function destroy(Contact $contact): ContactResource
    {
        $contact = $this->contactRepository->delete($contact);

        return ContactResource::make($contact);
    }
}
