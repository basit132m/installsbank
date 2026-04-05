<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdvertiserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'advertiser')->with('advertiserProfile');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $advertisers = $query->latest()->paginate(20);
        return view('admin.advertisers.index', compact('advertisers'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'advertiser') abort(404);
        $user->load('advertiserProfile');
        $campaigns = $user->campaigns()->with('payments')->latest()->get();
        return view('admin.advertisers.show', compact('user', 'campaigns'));
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        return back()->with('success', 'Advertiser suspended.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'Advertiser activated.');
    }
}
