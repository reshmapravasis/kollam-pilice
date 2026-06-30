<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug = null)
    {
        if ($slug === null) {
            $page = \App\Models\Page::where('slug', 'home')
                ->where('is_published', true)
                ->first();
            if (!$page) {
                return view('welcome');
            }
        } else {
            $page = \App\Models\Page::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        }

        return view('page', compact('page'));
    }

    public function storeInquiry(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry = \App\Models\Inquiry::create($validated);

        $mailSent = false;
        $recipientEmail = \App\Models\Setting::get('email') ?: config('mail.from.address');
        if ($recipientEmail) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\InquiryReceived($inquiry->toArray()));
                $mailSent = true;
            } catch (\Exception $e) {
                // Log the error but don't stop the user experience if mail fails
                \Illuminate\Support\Facades\Log::error('Failed to send inquiry email: ' . $e->getMessage());
            }
        }

        if ($mailSent || !$recipientEmail) {
            return back()->with('success_en', 'Your inquiry has been submitted successfully! We will contact you soon.')
                         ->with('success_ml', 'നിങ്ങളുടെ അന്വേഷണം വിജയകരമായി സമർപ്പിച്ചു! ഞങ്ങൾ നിങ്ങളെ ഉടൻ ബന്ധപ്പെടുന്നതാണ്.');
        } else {
            return back()->with('error_en', 'Your inquiry was saved, but there was an issue sending the email notification.')
                         ->with('error_ml', 'നിങ്ങളുടെ അന്വേഷണം സംരക്ഷിച്ചു, എന്നാൽ ഇമെയിൽ അയക്കുന്നതിൽ ഒരു പ്രശ്നമുണ്ടായി.');
        }
    }
}
