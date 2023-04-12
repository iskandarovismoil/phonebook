<?php

namespace App\Interfaces;

use App\Http\Requests\ContactStoreRequest;
use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function getAll();

    public function getById($contactId);

    public function create(ContactStoreRequest $request);

    public function update(ContactStoreRequest $request, $contactId);

    public function delete(Contact $contact);
}
