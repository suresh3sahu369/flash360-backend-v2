<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:newsletters,email']);

        Newsletter::create(['email' => $request->email]);

        return response()->json(['message' => 'Subscribed successfully!']);
    }
}