<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation (Ab naye fields bhi check honge)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20', // New Field
            'city' => 'required|string|max:255',   // New Field
            'state' => 'required|string|max:255',  // New Field
            'message' => 'required|string',
        ]);

        // 2. Save to Database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile, // Saving Mobile
            'city' => $request->city,     // Saving City
            'state' => $request->state,   // Saving State
            'message' => $request->message,
        ]);

        // 3. Return Success
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!'
        ], 201);
    }
}