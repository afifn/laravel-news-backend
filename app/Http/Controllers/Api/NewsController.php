<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $relationship = [
            'category' => function ($q) {
                $q->select('*');
            }
        ];
        $news = News::with($relationship)
            ->orderBy('id_news', 'DESC')
            ->paginate(5);
        return response()->json([
            'error' => false,
            'message' => 'successfully fetch data',
            'data' => $news
        ]);
    }

    public function show($id = null)
    {
        $relationship = [
            'category' => function ($q) {
                $q->select('*');
            }
        ];
        $news = News::with($relationship)
            ->where('id_news', $id)
            ->orderBy('id_news', 'DESC')
            ->get();
        if ($news) {
            return response()->json([
                'error' => false,
                'message' => 'success',
                'data' => $news
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'error',
                'data' => null
            ]);
        }
    }

    public function add(Request $request)
    {
        $validation = [
            'id_category' => 'required|integer',
            'title' => 'required|string|max:255|unique:news,title',
            'author' => 'required|string|max:100',
            'content' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }
        $data = [
            'id_category' => $request->input('id_category'),
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'content' => $request->input('content'),
            'slug' => Str::slug($request->input('title')),
            // 'poster' => null,
            'created_at' => now()
        ];
        if ($request->validate($validation)) {
            News::create($data);
            $response = [
                'error' => false,
                'message' => 'successfully added data',
            ];
            return response()->json($response);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_category' => 'integer',
            'title' => 'string',
            'author' => 'string',
            'content' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }
        $data = [
            'id_category' => $request->input('id_category'),
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'content' => $request->input('content')
        ];
        $news = News::find($id);
        if ($news) {
            $news->update($data);

            $response = [
                'error' => false,
                'message' => 'successfully updated data'
            ];
            return response()->json($response);
        }
    }
    public function destroy($id = null)
    {
        $data = News::find($id);
        if ($data) {
            $data->delete();
            $response = [
                'error' => false,
                'message' => 'successfully deleting item'
            ];
            return response()->json($response);
        } else {
            $response = [
                'error' => true,
            ];
            return response()->json($response);
        }
    }
}
