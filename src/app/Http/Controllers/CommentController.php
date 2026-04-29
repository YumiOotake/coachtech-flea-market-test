<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $item_id)
    {
        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', ['item_id' => $item_id]);
    }
}
