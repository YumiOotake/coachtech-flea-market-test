<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page');

        $items = $user->items()->get();

        if ($page === 'sell') {
            $items = $user->items()->get();
        } elseif ($page === 'buy') {
            $items = $user->orders()->with('item')->get()->pluck('item');
        }

        return view('mypage/index', compact('items', 'user'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('mypage/edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        if ($request->file('image')) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $imagePath = $request->file('image')->store('uploads', 'public');
        } else {
            $imagePath = $profile->profile_image ?? null;
        }

        $profile->profile_image = $imagePath;
        $profile->postal_code = $request->postal_code;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();

        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->route('items.index');
    }
}
