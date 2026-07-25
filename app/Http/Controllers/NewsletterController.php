<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns|max:255|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'email' => strtolower($validated['email']),
            'ip_address' => $request->ip(),
            'status' => 'active',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'You have been successfully subscribed.',
                'subscriber_id' => $subscriber->id,
            ], 201);
        }

        return back()->with('success', 'You have been successfully subscribed to our newsletter.');
    }
}
