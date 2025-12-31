<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ContactFormRequest;
use App\Models\ContactMessage;
use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $message = ContactMessage::create($request->validated());

        // Send email to admin
        Mail::to(config('mail.from.address'))->send(new ContactFormSubmitted($message));

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
}
