<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $validate = [
            'id_category' => 'required|integer',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'content' => 'required|string',
        ];
        $data = [
            'id_category' => $request->input('id_category'),
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'content' => $request->input('content'),
            'slug' => Str::slug($request->input('title')),
            // 'poster' => null,
            'created_at' => now()
        ];
        if ($request->validate($validate)) {
            News::create($data);
            $response = [
                'error' => false,
                'message' => 'success added data',
            ];
            return response()->json($response);
        }
    }
}
