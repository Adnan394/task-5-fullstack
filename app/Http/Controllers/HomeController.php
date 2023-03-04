<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{


    public function index() {
        $data = Post::paginate(5);
        return [
            'message' => 'Success',
            'data' => $data,
        ];
    }

    public function store(Request $request) {
        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category_id,
        ];

        $save = Post::create($data);
        if($save) {
            return response()->json([
                'message' => 'Success',
                'data' => [
                    'title' => $save->title,
                    'content' => $save->content,
                    'image' => $save->image,
                    'user' => $save->user->name,
                    'category' => $save->category->name,
                ],
            ]);
        }
    }

    public function show($id) {
        $data = Post::where('id', $id)->get();
        return response()->json([
            'message' => 'Success',
            'data' => $data,
        ]);
    }

    public function update($id, Request $request) {
        $data = Post::findOrFail($id);
        
        if($data->user_id == Auth::user()->id) {
            $data->update($request->all());
            $data->user_id = Auth::user()->id;
            $data->save();
            return response()->json($data);
        }else {
            return response()->json(['message' => 'Tidak dapat mengubah data milik orang']);
        }
    }

    public function destroy($id) {
        $data = Post::findOrFail($id);
        $data->delete();

        if($data) {
            return response()->json(['messsage' => 'data ' . $data->id . ' was Deleted']);
        }
    }
}