<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
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
        $keyword = $request->query('keyword');

        if ($tab === 'mylist' && !auth()->check()) {
            $items = collect();
            return view('items/index', compact('items'));
        }

        if ($tab === 'mylist') {
            $items = $user->likedItems();
        } elseif (auth()->check()) {
            $items = Item::where('user_id', '!=', auth()->id());
        } else {
            $items = Item::query();
        }

        if (!empty($keyword)) {
            $items->where('name', 'like', "%{$keyword}%");
        }

        $items = $items->get();

        return view('items/index', compact('items', 'keyword'));
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

    public function store(ExhibitionRequest $request)
    {
        $imagePath = $request->file('image')->store('uploads', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'condition_id' => $request->condition_id,
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
