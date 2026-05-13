<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;

class LikeController extends Controller
{
    public function store($item_id)
    {
        $item = Item::findOrFail($item_id);

        Like::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
        ]);

        return redirect()->route('items.show', ['item_id' => $item_id]);
    }

    public function destroy($item_id)
    {
        $item = Item::findOrFail($item_id);

        Like::where('user_id', auth()->id())
            ->where('item_id', $item->id)
            ->delete();

        return redirect()->route('items.show', ['item_id' => $item_id]);
    }
}
