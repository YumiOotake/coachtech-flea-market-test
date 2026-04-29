<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab');

        if ($tab === 'mylist') {
            if (!auth()->check()) {
                $items = collect();
            } else {
                $items = $user->likedItems()->get();
            }
        } else {
            if (!auth()->check()) {
                $items = Item::all();
            } else {
                $items = Item::where('user_id', '!=', auth()->id())->get();
            }
        }

        return view('items/index', compact('items'));
    }

    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        $imagePath = $request->file('image')->store('uploads', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'condition_id' =>$request->condition_id,
            'image' => $imagePath,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $item->categories()->attach($request->category_id);

        return redirect()->route('mypage.index');
    }
}
