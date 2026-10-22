<?php

namespace App\\Http\\Controllers;

use App\\Models\\Show;
use Illuminate\\Http\\RedirectResponse;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Mail;
use Illuminate\\View\\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $show = null;
        $slug = $request->query('show');

        if (is_string($slug) && $slug !== '') {
            $show = Show::published()
                ->with(['categories', 'genres'])
                ->where('slug', $slug)
                ->firstOrFail();
        }

        return view('contact.create', compact('show'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'company' => ['nullable', 'string', 'max:160'],
            'inquiry_type' => ['required', 'in:General Inquiry,Content Licensing & Distribution,Production & Collaboration,Media Services,Press & Media,Careers,Other'],
            'message' => ['required', 'string', 'max:5000'],
            'show_slug' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        $show = null;

        if (filled($data['show_slug'] ?? null)) {
            $show = Show::published()
                ->where('slug', $data['show_slug'])
                ->first();

            if (! $show) {
                return back()
                    ->withInput()
                    ->withErrors(['show_slug' => __('The selected show is no longer available. Please choose it again.')]);
            }
        }

        $recipient = config('services.contact_to');

        if (blank($recipient)) {
            return back()
                ->withInput()
                ->withErrors(['form' => 'The contact form email recipient has not been configured yet.']);
        }

        $subject = $show
            ? 'MLI Show Inquiry: ' . $show->title_en
            : 'MLI Website Inquiry: ' . $data['inquiry_type'];

        $body = implode("\n", [
            'A new inquiry was submitted through the MLI website.',
            '',
            'Name: ' . $data['name'],
            'Email: ' . $data['email'],
            'Company: ' . ($data['company'] ?: 'Not provided'),
            'Inquiry type: ' . $data['inquiry_type'],
            'Show: ' . ($show ? $show->title_en : 'Not show-specific'),
            '',
            'Message:',
            $data['message'],
        ]);

        Mail::raw($body, function ($mail) use ($recipient, $data, $subject): void {
            $mail->to($recipient)
                ->replyTo($data['email'], $data['name'])
                ->subject($subject);
        });

        return redirect()
            ->route('contact.create', $show ? ['show' => $show->slug] : [])
            ->with('success', app()->getLocale() === 'ar'
                ? 'شكراً لتواصلك معنا. تم إرسال رسالتك.'
                : 'Thank you for contacting MLI. Your message has been sent.');
    }
}
